<?php

namespace App\Jobs\Notification;

use App\Actions\Notification\AggregateNotificationStatusAction;
use App\Actions\Notification\SendFcmNativeNotificationAction;
use App\Enums\FcmNativeDeliveryStatus;
use App\Models\NotificationFcmDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

final class DeliverFcmNativeNotificationJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 30;

    public function __construct(
        public readonly NotificationFcmDelivery $delivery,
    ) {}

    public function handle(
        SendFcmNativeNotificationAction $sender,
        AggregateNotificationStatusAction $aggregator,
    ): void {
        $this->delivery->refresh();

        /*
         * Do not send a delivery that has already reached
         * a terminal state.
         */
        if (
            in_array(
                $this->delivery->status,
                [
                    FcmNativeDeliveryStatus::SENT,
                    FcmNativeDeliveryStatus::FAILED,
                ],
                true,
            )
        ) {
            return;
        }

        $this->delivery->update([
            'status' => FcmNativeDeliveryStatus::PROCESSING,
            'processing_at' => now(),
            'attempts' => $this->delivery->attempts + 1,
            'error_message' => null,
        ]);

        try {
            $sender->handle(
                notification: $this->delivery->notification,
                target: $this->delivery->target_value,
            );

            $this->delivery->update([
                'status' => FcmNativeDeliveryStatus::SENT,
                'sent_at' => now(),
                'completed_at' => now(),
                'error_message' => null,
            ]);

            $aggregator->handle(
                $this->delivery->notification->refresh(),
            );
        } catch (Throwable $exception) {
            /*
             * Keep the delivery in PROCESSING while Laravel
             * retries the job.
             *
             * The final failed() callback will move it to FAILED
             * after all attempts are exhausted.
             */
            $this->delivery->update([
                'status' => FcmNativeDeliveryStatus::PROCESSING,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(
        ?Throwable $exception,
    ): void {
        $this->delivery->refresh();

        if ($this->delivery->status === FcmNativeDeliveryStatus::SENT) {
            return;
        }

        $this->delivery->update([
            'status' => FcmNativeDeliveryStatus::FAILED,
            'error_message' => $exception?->getMessage(),
            'completed_at' => now(),
        ]);

        app(AggregateNotificationStatusAction::class)
            ->handle(
                $this->delivery->notification->refresh(),
            );
    }
}