<?php

namespace App\Jobs\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Actions\Fcm\SendFcmNotificationAction;
use App\Models\NotificationDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;

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
            ]);

            $this->delivery->update([
                'status' => NotificationDeliveryStatus::COMPLETED,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $this->delivery->update([
                'status' => NotificationDeliveryStatus::FAILED,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}