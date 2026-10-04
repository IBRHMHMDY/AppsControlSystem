<?php

namespace App\Actions\Notification;

use App\Enums\FcmNativeDeliveryStatus;
use App\Enums\NotificationDeliveryStatus;
use App\Enums\NotificationStatus;
use App\Models\Notification;

final class AggregateNotificationStatusAction
{
    public function handle(Notification $notification): Notification
    {
        $notification->load([
            'deliveries',
            'fcmDeliveries',
        ]);

        $deviceDeliveries = $notification->deliveries;
        $fcmDeliveries = $notification->fcmDeliveries;

        $totalDeliveries = $deviceDeliveries->count()
            + $fcmDeliveries->count();

        if ($totalDeliveries === 0) {
            return $notification;
        }

        $hasPendingDeviceDelivery = $deviceDeliveries->contains(
            fn ($delivery): bool => in_array(
                $delivery->status,
                [
                    NotificationDeliveryStatus::QUEUED,
                    NotificationDeliveryStatus::PROCESSING,
                    NotificationDeliveryStatus::RETRYING,
                ],
                true,
            ),
        );

        $hasPendingFcmDelivery = $fcmDeliveries->contains(
            fn ($delivery): bool => in_array(
                $delivery->status,
                [
                    FcmNativeDeliveryStatus::QUEUED,
                    FcmNativeDeliveryStatus::PROCESSING,
                ],
                true,
            ),
        );

        /*
         * A notification remains pending while at least one
         * delivery can still complete.
         */
        if ($hasPendingDeviceDelivery || $hasPendingFcmDelivery) {
            $notification->update([
                'status' => NotificationStatus::PENDING,
            ]);

            return $notification->refresh();
        }

        $hasFailedDeviceDelivery = $deviceDeliveries->contains(
            fn ($delivery): bool => in_array(
                $delivery->status,
                [
                    NotificationDeliveryStatus::FAILED,
                    NotificationDeliveryStatus::INVALID_TOKEN,
                ],
                true,
            ),
        );

        $hasFailedFcmDelivery = $fcmDeliveries->contains(
            fn ($delivery): bool => $delivery->status
                === FcmNativeDeliveryStatus::FAILED,
        );

        /*
         * Failure becomes final only after there are
         * no pending deliveries left.
         */
        if ($hasFailedDeviceDelivery || $hasFailedFcmDelivery) {
            $notification->update([
                'status' => NotificationStatus::FAILED,
            ]);

            return $notification->refresh();
        }

        $allDeviceDeliveriesSent = $deviceDeliveries->every(
            fn ($delivery): bool => $delivery->status
                === NotificationDeliveryStatus::SENT,
        );

        $allFcmDeliveriesSent = $fcmDeliveries->every(
            fn ($delivery): bool => $delivery->status
                === FcmNativeDeliveryStatus::SENT,
        );

        if ($allDeviceDeliveriesSent && $allFcmDeliveriesSent) {
            $notification->update([
                'status' => NotificationStatus::SENT,
                'sent_at' => now(),
            ]);
        }

        return $notification->refresh();
    }
}