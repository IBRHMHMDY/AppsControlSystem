<?php

namespace App\Actions\Notification;

use App\Enums\NotificationDeliveryStatus;
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
            if (
                ! in_array(
                    $delivery->status,
                    [
                        NotificationDeliveryStatus::QUEUED,
                        NotificationDeliveryStatus::RETRYING,
                    ],
                    true,
                )
            ) {
                continue;
            }

            try {
                DeliverNotificationJob::dispatch(
                    delivery: $delivery,
                )->afterCommit();
            } catch (Throwable $exception) {
                $delivery->update([
                    'status' => NotificationDeliveryStatus::FAILED,
                    'error_message' => $exception->getMessage(),
                    'completed_at' => now(),
                ]);

                app(AggregateNotificationStatusAction::class)
                    ->handle(
                        $delivery->notification->refresh(),
                    );
            }
        }
    }
}