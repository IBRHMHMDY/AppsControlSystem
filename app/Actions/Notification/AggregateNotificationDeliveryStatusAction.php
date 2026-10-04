<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Enums\NotificationStatus;
use App\Models\Notification;

final class AggregateNotificationDeliveryStatusAction
{
    public function handle(Notification $notification): Notification
    {
        $deliveries = $notification->deliveries()->get();

        if ($deliveries->isEmpty()) {
            return $notification->refresh();
        }

        $hasFailure = $deliveries->contains(
            fn ($delivery) => in_array(
                $delivery->status,
                [
                    NotificationDeliveryStatus::FAILED,
                    NotificationDeliveryStatus::INVALID_TOKEN,
                ],
                true,
            ),
        );

        if ($hasFailure) {
            $notification->update([
                'status' => NotificationStatus::FAILED,
            ]);

            return $notification->refresh();
        }

        $allCompleted = $deliveries->every(
            fn ($delivery) => $delivery->status === NotificationDeliveryStatus::COMPLETED,
        );

        if ($allCompleted) {
            $notification->update([
                'status' => NotificationStatus::SENT,
                'sent_at' => now(),
            ]);
        }

        return $notification->refresh();
    }
}