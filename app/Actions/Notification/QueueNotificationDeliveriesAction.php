<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
use App\Enums\NotificationStatus;
use App\Jobs\Notification\DeliverNotificationJob;
use App\Models\NotificationDelivery;
use Illuminate\Support\Collection;
use Throwable;

final class QueueNotificationDeliveriesAction
{
    /**
     * @param Collection<int, NotificationDelivery> $deliveries
     */
    public function handle(Collection $deliveries): void
    {
        foreach ($deliveries as $delivery) {
            try {
                DeliverNotificationJob::dispatch(
                    delivery: $delivery,
                )->afterCommit();
            } catch (Throwable $exception) {
                $delivery->update([
                    'status' => NotificationDeliveryStatus::FAILED,
                    'error_message' => $exception->getMessage(),
                ]);

                $delivery->notification()->update([
                    'status' => NotificationStatus::FAILED,
                ]);
            }
        }
    }
}