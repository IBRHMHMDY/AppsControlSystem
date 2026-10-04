<?php

namespace App\Jobs\Notification;

use App\Actions\Device\DeactivateInvalidFcmTokenAction;
use App\Actions\Fcm\ClassifyFcmExceptionAction;
use App\Actions\Fcm\SendFcmNotificationAction;
use App\Actions\Notification\AggregateNotificationStatusAction;
use App\Enums\NotificationDeliveryStatus;
use App\Models\NotificationDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;

#[Tries(3)]
#[Backoff(3)]
#[Timeout(60)]
final class DeliverNotificationJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public readonly NotificationDelivery $delivery,
    ) {}

    public function handle(
        SendFcmNotificationAction $action,
        ClassifyFcmExceptionAction $classifier,
        DeactivateInvalidFcmTokenAction $deactivateInvalidToken,
        AggregateNotificationStatusAction $aggregateStatus,
    ): void {
        $this->delivery->refresh();

        /*
         * Do not process a delivery that has already reached
         * a terminal state.
         */
        if (
            in_array(
                $this->delivery->status,
                [
                    NotificationDeliveryStatus::SENT,
                    NotificationDeliveryStatus::INVALID_TOKEN,
                ],
                true,
            )
        ) {
            return;
        }

        $attempt = $this->delivery->attempts + 1;

        $this->delivery->update([
            'status' => NotificationDeliveryStatus::PROCESSING,
            'processing_at' => now(),
            'attempts' => $attempt,
            'error_message' => null,
        ]);

        try {
            $action->handle(
                device: $this->delivery->device,
                title: $this->delivery->notification->title,
                body: $this->delivery->notification->body,
                data: $this->delivery->notification->data_payload ?? [],
            );

            $this->delivery->update([
                'status' => NotificationDeliveryStatus::SENT,
                'sent_at' => now(),
                'completed_at' => now(),
                'error_message' => null,
            ]);

            $aggregateStatus->handle(
                $this->delivery->notification->refresh(),
            );
        } catch (Throwable $exception) {
            $status = $classifier->handle($exception);

            if ($status === NotificationDeliveryStatus::INVALID_TOKEN) {
                $this->delivery->update([
                    'status' => NotificationDeliveryStatus::INVALID_TOKEN,
                    'error_message' => $exception->getMessage(),
                    'completed_at' => now(),
                ]);

                $deactivateInvalidToken->handle(
                    $this->delivery->device,
                );

                $aggregateStatus->handle(
                    $this->delivery->notification->refresh(),
                );

                return;
            }

            /*
             * Temporary/retryable error.
             */
            $this->delivery->update([
                'status' => NotificationDeliveryStatus::RETRYING,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->delivery->update([
            'status' => NotificationDeliveryStatus::FAILED,
            'error_message' => $exception?->getMessage(),
            'completed_at' => now(),
        ]);

        app(AggregateNotificationStatusAction::class)
            ->handle(
                $this->delivery->notification->refresh(),
            );
    }
}