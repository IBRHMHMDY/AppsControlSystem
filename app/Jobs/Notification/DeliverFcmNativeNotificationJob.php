<?php

namespace App\Jobs\Notification;

use App\Actions\Notification\AggregateNotificationStatusAction;
use App\Actions\Notification\SendFcmNativeNotificationAction;
use App\Enums\FcmNativeDeliveryStatus;
use App\Models\NotificationFcmDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class DeliverFcmNativeNotificationJob implements ShouldQueue
{
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
        $this->delivery->increment('attempts');

        $this->delivery->update([
            'status' => FcmNativeDeliveryStatus::PROCESSING,
            'processing_at' => now(),
            'error_message' => null,
        ]);

        $sender->handle(
            notification: $this->delivery->notification,
            target: $this->delivery->target_value,
        );

        $this->delivery->update([
            'status' => FcmNativeDeliveryStatus::SENT,
            'sent_at' => now(),
            'completed_at' => now(),
        ]);

        $aggregator->handle(
            $this->delivery->notification->refresh(),
        );
    }

    public function failed(
        ?Throwable $exception,
    ): void {
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