<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;

final class CancelNotificationAction
{
    public function handle(Notification $notification): Notification
    {
        if (
            ! in_array(
                $notification->status,
                [
                    NotificationStatus::DRAFT,
                    NotificationStatus::SCHEDULED,
                    NotificationStatus::PENDING,
                ],
                true,
            )
        ) {
            throw new ApiException(
                message: 'This notification cannot be cancelled.',
                status: 422,
            );
        }

        $notification->update([
            'status' => NotificationStatus::CANCELLED,
        ]);

        return $notification->refresh();
    }
}