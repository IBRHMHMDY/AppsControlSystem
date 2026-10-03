<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;
use DateTimeInterface;

final class ScheduleNotificationAction
{
    public function handle(
        Notification $notification,
        DateTimeInterface $scheduledAt,
    ): Notification {
        if ($notification->status !== NotificationStatus::DRAFT) {
            throw new ApiException(
                message: 'Only draft notifications can be scheduled.',
                status: 422,
            );
        }

        $notification->update([
            'status' => NotificationStatus::SCHEDULED,
            'scheduled_at' => $scheduledAt,
        ]);

        return $notification->refresh();
    }
}