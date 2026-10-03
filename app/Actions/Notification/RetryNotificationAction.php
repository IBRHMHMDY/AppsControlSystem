<?php

namespace App\Actions\Notification;

use App\Enums\NotificationStatus;
use App\Exceptions\ApiException;
use App\Models\Notification;

final class RetryNotificationAction
{
    public function handle(Notification $notification): Notification
    {
        if ($notification->status !== NotificationStatus::SENT) {
            throw new ApiException(
                message: 'Only sent notifications can be retried.',
                status: 422,
            );
        }

        $notification->update([
            'status' => NotificationStatus::PENDING,
            'sent_at' => null,
        ]);

        return $notification->refresh();
    }
}