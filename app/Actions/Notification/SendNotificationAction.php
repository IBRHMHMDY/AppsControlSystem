<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Enums\NotificationTargetType;
use App\Exceptions\ApiException;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

final class SendNotificationAction
{
    public function __construct(
        private readonly DispatchNotificationDeliveryAction $dispatchDeviceDelivery,
        private readonly QueueNotificationDeliveriesAction $queueDeviceDeliveries,
        private readonly DispatchNotificationFcmDeliveryAction $dispatchFcmDelivery,
        private readonly QueueNotificationFcmDeliveryAction $queueFcmDelivery,
        private readonly ValidateNotificationTargetAction $validateTarget,
    ) {}

    public function handle(Notification $notification): Notification
    {
        if ($notification->status !== NotificationStatus::DRAFT) {
            throw new ApiException(
                message: 'Only draft notifications can be sent.',
                status: 422,
            );
        }

        return DB::transaction(function () use ($notification): Notification {
            $this->validateTarget->handle($notification);
            $notification->update([
                'status' => NotificationStatus::PENDING,
            ]);

            match ($notification->target_type) {
                NotificationTargetType::DEVICE,
                NotificationTargetType::DEVICE_TOKEN,
                NotificationTargetType::DEVICES,
                NotificationTargetType::DEVICE_SELECTION
                    => $this->dispatchDevice($notification),

                NotificationTargetType::TOPIC,
                NotificationTargetType::CONDITION
                    => $this->dispatchFcmNative($notification),
            };

            return $notification->refresh();
        });
    }

    private function dispatchDevice(
        Notification $notification,
    ): void {
        $deliveries = $this->dispatchDeviceDelivery->handle(
            $notification,
        );

        $this->queueDeviceDeliveries->handle(
            $deliveries,
        );
    }

    private function dispatchFcmNative(
        Notification $notification,
    ): void {
        $delivery = $this->dispatchFcmDelivery->handle(
            $notification,
        );

        $this->queueFcmDelivery->handle(
            $delivery,
        );
    }
}