<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;

final class SendNotificationAction
{
    public function handle(Notification $notification): Notification
    {
        if ($notification->status !== NotificationStatus::DRAFT) {
            throw new ApiException(
                message: 'Only draft notifications can be sent.',
                status: 422,
            );
        }

        $notification->update([
            'status' => NotificationStatus::PENDING,
        ]);

        return $notification->refresh();
    }
}