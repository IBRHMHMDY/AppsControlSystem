<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Enums\FcmNativeDeliveryStatus;
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

        $hasFailedDelivery = $deviceDeliveries->contains(
            fn ($delivery): bool => $delivery->status === NotificationDeliveryStatus::FAILED
                || $delivery->status === NotificationDeliveryStatus::INVALID_TOKEN,
        );

        $hasFailedFcmDelivery = $fcmDeliveries->contains(
            fn ($delivery): bool => $delivery->status === FcmNativeDeliveryStatus::FAILED,
        );

        if ($hasFailedDelivery || $hasFailedFcmDelivery) {
            $notification->update([
                'status' => NotificationStatus::FAILED,
            ]);

            return $notification->refresh();
        }

        $hasPendingDelivery = $deviceDeliveries->contains(
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

        if ($hasPendingDelivery || $hasPendingFcmDelivery) {
            $notification->update([
                'status' => NotificationStatus::PENDING,
            ]);

            return $notification->refresh();
        }

        $allDeviceDeliveriesCompleted = $deviceDeliveries->every(
            fn ($delivery): bool => $delivery->status === NotificationDeliveryStatus::COMPLETED,
        );

        $allFcmDeliveriesSent = $fcmDeliveries->every(
            fn ($delivery): bool => $delivery->status === FcmNativeDeliveryStatus::SENT,
        );

        if ($allDeviceDeliveriesCompleted && $allFcmDeliveriesSent) {
            $notification->update([
                'status' => NotificationStatus::SENT,
            ]);
        }

        return $notification->refresh();
    }
}