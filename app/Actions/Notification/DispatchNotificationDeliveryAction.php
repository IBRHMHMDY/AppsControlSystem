<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Jobs\Notification\DeliverNotificationJob;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use Illuminate\Database\Eloquent\Collection;

final class DispatchNotificationDeliveryAction
{
    public function __construct(
        private readonly ResolveNotificationDevicesAction $resolver,
    ) {}

    public function handle(Notification $notification): void
    {
        $devices = $this->resolver->handle($notification);

        foreach ($devices as $device) {
            $delivery = NotificationDelivery::query()->create([
                'notification_id' => $notification->id,
                'device_id' => $device->id,
                'status' => NotificationDeliveryStatus::QUEUED,
                'queued_at' => now(),
            ]);

            DeliverNotificationJob::dispatch(
                delivery: $delivery,
            );
        }
    }
}