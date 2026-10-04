<?php

namespace App\Actions\Notification;

use App\Enums\NotificationTargetType;
use App\Exceptions\ApiException;
use App\Models\Notification;

final class ValidateNotificationTargetAction
{
    public function handle(Notification $notification): void
    {
        if (blank($notification->target_value)) {
            throw new ApiException(
                message: 'Notification target value is required.',
                status: 422,
            );
        }

        if (
            $notification->target_type === NotificationTargetType::CONDITION
            && strlen((string) $notification->target_value) > 1024
        ) {
            throw new ApiException(
                message: 'Notification condition is too long.',
                status: 422,
            );
        }
    }
}