<?php

namespace App\Actions\Notification;

use App\Jobs\Notification\DeliverNotificationJob;
use App\Models\NotificationDelivery;
use Illuminate\Support\Collection;

final class QueueNotificationDeliveriesAction
{
    /**
     * @param Collection<int, NotificationDelivery> $deliveries
     */
    public function handle(Collection $deliveries): void
    {
        foreach ($deliveries as $delivery) {
            DeliverNotificationJob::dispatch(
                delivery: $delivery,
            );
        }
    }
}