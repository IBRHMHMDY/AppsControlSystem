<?php

namespace App\Jobs\Notification;

use App\Actions\Device\DeactivateInvalidFcmTokenAction;
use App\Actions\Fcm\ClassifyFcmExceptionAction;
use App\Actions\Fcm\SendFcmNotificationAction;
use App\Actions\Notification\AggregateNotificationDeliveryStatusAction;
use App\Enums\NotificationDeliveryStatus;
use App\Models\NotificationDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Throwable;

#[Tries(3)]
#[Backoff(3)]
#[Timeout(60)]
final class DeliverNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly NotificationDelivery $delivery,
    ) {}

    public function handle(
        SendFcmNotificationAction $action,
        ClassifyFcmExceptionAction $classifier,
        DeactivateInvalidFcmTokenAction $deactivateInvalidToken,
        AggregateNotificationDeliveryStatusAction $aggregateStatus,
    ): void {
        $this->delivery->update([
            'status' => NotificationDeliveryStatus::PROCESSING,
            'processing_at' => now(),
            'attempts' => $this->delivery->attempts + 1,
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
            ]);

            $this->delivery->update([
                'status' => NotificationDeliveryStatus::COMPLETED,
            ]);

            $aggregateStatus->handle(
                $this->delivery->notification->refresh(),
            );
        } catch (Throwable $exception) {
            $status = $classifier->handle($exception);

            $this->delivery->update([
                'status' => $status,
                'error_message' => $exception->getMessage(),
            ]);

            if ($status === NotificationDeliveryStatus::INVALID_TOKEN) {
                $deactivateInvalidToken->handle(
                    $this->delivery->device,
                );

                $aggregateStatus->handle(
                    $this->delivery->notification->refresh(),
                );

                return;
            }

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->delivery->update([
            'status' => NotificationDeliveryStatus::FAILED,
            'error_message' => $exception?->getMessage(),
        ]);

        app(AggregateNotificationDeliveryStatusAction::class)
            ->handle(
                $this->delivery->notification->refresh(),
            );
    }
}
