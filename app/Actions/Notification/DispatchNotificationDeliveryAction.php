<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Models\Notification;
use App\Models\NotificationDelivery;
use Illuminate\Support\Collection;

final class DispatchNotificationDeliveryAction
{
    public function __construct(
        private readonly ResolveNotificationDevicesAction $resolver,
    ) {}

    /**
     * @return Collection<int, NotificationDelivery>
     */
    public function handle(Notification $notification): Collection
    {
        $devices = $this->resolver->handle($notification);

        return $devices
            ->map(
                fn ($device): NotificationDelivery => NotificationDelivery::query()
                    ->firstOrCreate(
                        [
                            'notification_id' => $notification->id,
                            'device_id' => $device->id,
                        ],
                        [
                            'status' => NotificationDeliveryStatus::QUEUED,
                            'queued_at' => now(),
                        ],
                    ),
            )
            ->filter(
                fn (NotificationDelivery $delivery): bool => in_array(
                    $delivery->status,
                    [
                        NotificationDeliveryStatus::QUEUED,
                        NotificationDeliveryStatus::RETRYING,
                    ],
                    true,
                ),
            )
            ->values();
    }
}