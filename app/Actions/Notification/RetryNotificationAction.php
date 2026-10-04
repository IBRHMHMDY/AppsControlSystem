<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Enums\NotificationStatus;
use App\Enums\FcmNativeDeliveryStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

final class RetryNotificationAction
{
    public function __construct(
        private readonly DispatchNotificationDeliveryAction $dispatchDelivery,
        private readonly QueueNotificationDeliveriesAction $queueDeliveries,
        private readonly ResolveNotificationFcmTargetAction $resolveFcmTarget,
        private readonly QueueFcmNativeDeliveryAction $queueFcmNativeDelivery,
    ) {}

    public function handle(Notification $notification): Notification
    {
        if ($notification->status !== NotificationStatus::SENT) {
            throw new ApiException(
                message: 'Only sent notifications can be retried.',
                status: 422,
            );
        }

        return DB::transaction(function () use ($notification): Notification {
            $notification->update([
                'status' => NotificationStatus::PENDING,
                'sent_at' => null,
            ]);

            /*
             * Reset existing device deliveries that can be retried.
             */
            $notification->deliveries()
                ->whereIn(
                    'status',
                    [
                        NotificationDeliveryStatus::COMPLETED,
                        NotificationDeliveryStatus::FAILED,
                        NotificationDeliveryStatus::INVALID_TOKEN,
                    ],
                )
                ->update([
                    'status' => NotificationDeliveryStatus::QUEUED,
                    'attempts' => 0,
                    'error_message' => null,
                    'queued_at' => now(),
                    'processing_at' => null,
                    'sent_at' => null,
                    'completed_at' => null,
                ]);

            /*
             * Queue existing device deliveries.
             */
            $deliveries = $notification->deliveries()
                ->whereIn(
                    'status',
                    [
                        NotificationDeliveryStatus::QUEUED,
                        NotificationDeliveryStatus::RETRYING,
                    ],
                )
                ->get();

            $this->queueDeliveries->handle($deliveries);

            /*
             * Re-queue FCM Native deliveries.
             */
            $fcmDeliveries = $notification->fcmDeliveries()
                ->whereIn(
                    'status',
                    [
                        FcmNativeDeliveryStatus::SENT,
                        FcmNativeDeliveryStatus::FAILED,
                    ],
                )
                ->get();

            foreach ($fcmDeliveries as $delivery) {
                $delivery->update([
                    'status' => FcmNativeDeliveryStatus::QUEUED,
                    'attempts' => 0,
                    'error_message' => null,
                    'queued_at' => now(),
                    'processing_at' => null,
                    'sent_at' => null,
                    'completed_at' => null,
                ]);

                $this->queueFcmNativeDelivery->handle($delivery);
            }

            /*
             * If no existing FCM Native delivery exists,
             * create one for TOPIC / CONDITION notifications.
             */
            if (
                $notification->fcmDeliveries()->doesntExist()
                && in_array(
                    $notification->target_type,
                    [
                        \App\Enums\NotificationTargetType::TOPIC,
                        \App\Enums\NotificationTargetType::CONDITION,
                    ],
                    true,
                )
            ) {
                $target = $this->resolveFcmTarget->handle($notification);

                $delivery = $notification->fcmDeliveries()->create([
                    'target_type' => $notification->target_type,
                    'target_value' => $target,
                    'target_hash' => hash('sha256', $target),
                    'status' => FcmNativeDeliveryStatus::QUEUED,
                    'attempts' => 0,
                    'queued_at' => now(),
                ]);

                $this->queueFcmNativeDelivery->handle($delivery);
            }

            return $notification->refresh();
        });
    }
}