<?php

namespace App\Actions\Notification;

use App\Enums\FcmNativeDeliveryStatus;
use App\Models\Notification;
use App\Models\NotificationFcmDelivery;

final class CreateNotificationFcmDeliveryAction
{
    public function handle(
        Notification $notification,
    ): NotificationFcmDelivery {
        return NotificationFcmDelivery::query()->firstOrCreate(
            [
                'notification_id' => $notification->id,
                'target_type' => $notification->target_type,
                'target_hash' => hash(
                    'sha256',
                    (string) $notification->target_value,
                ),
            ],
            [
                'target_value' => $notification->target_value,
                'status' => FcmNativeDeliveryStatus::QUEUED,
                'queued_at' => now(),
                'attempts' => 0,
            ],
        );
    }
}