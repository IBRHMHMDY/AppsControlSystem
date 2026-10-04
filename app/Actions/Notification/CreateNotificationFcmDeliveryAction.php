<?php

namespace App\Actions\Notification;

use App\Enums\FcmNativeDeliveryStatus;
use App\Models\Notification;
use App\Models\NotificationFcmDelivery;

final class CreateNotificationFcmDeliveryAction
{
    public function handle(
        Notification $notification,
        string $target,
    ): NotificationFcmDelivery {
        $targetHash = hash('sha256', $target);

        return NotificationFcmDelivery::query()->firstOrCreate(
            [
                'notification_id' => $notification->id,
                'target_type' => $notification->target_type,
                'target_hash' => $targetHash,
            ],
            [
                'target_value' => $target,
                'status' => FcmNativeDeliveryStatus::QUEUED,
                'queued_at' => now(),
            ],
        );
    }
}