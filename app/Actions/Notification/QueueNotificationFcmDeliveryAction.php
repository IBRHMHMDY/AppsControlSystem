<?php

namespace App\Actions\Notification;

use App\Enums\FcmNativeDeliveryStatus;
use App\Jobs\Notification\DeliverFcmNativeNotificationJob;
use App\Models\NotificationFcmDelivery;
use Throwable;

final class QueueNotificationFcmDeliveryAction
{
    public function handle(
        NotificationFcmDelivery $delivery,
    ): void {
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