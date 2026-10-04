<?php

namespace App\Actions\Notification;

use App\Enums\FcmNativeDeliveryStatus;
use App\Jobs\Notification\DeliverFcmNativeNotificationJob;
use App\Models\NotificationFcmDelivery;
use Throwable;

final class QueueFcmNativeDeliveryAction
{
    public function handle(
        NotificationFcmDelivery $delivery,
    ): void {
        $delivery->update([
            'status' => FcmNativeDeliveryStatus::QUEUED,
            'queued_at' => now(),
        ]);

        try {
            DeliverFcmNativeNotificationJob::dispatch(
                delivery: $delivery,
            )->afterCommit();
        } catch (Throwable $exception) {
            $delivery->update([
                'status' => FcmNativeDeliveryStatus::FAILED,
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);

            throw $exception;
        }
    }
}