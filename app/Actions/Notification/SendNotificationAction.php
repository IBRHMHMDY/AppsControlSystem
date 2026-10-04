<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

final class SendNotificationAction
{
    public function __construct(
        private readonly DispatchNotificationDeliveryAction $dispatchDelivery,
        private readonly QueueNotificationDeliveriesAction $queueDeliveries,
    ) {}

    public function handle(Notification $notification): Notification
    {
        if ($notification->status !== NotificationStatus::DRAFT) {
            throw new ApiException(
                message: 'Only draft notifications can be sent.',
                status: 422,
            );
        }

        return DB::transaction(function () use ($notification) {
            $notification->update([
                'status' => NotificationStatus::PENDING,
            ]);

            $deliveries = $this->dispatchDelivery->handle($notification);

            $this->queueDeliveries->handle($deliveries);

            return $notification->refresh();
        });
    }
}