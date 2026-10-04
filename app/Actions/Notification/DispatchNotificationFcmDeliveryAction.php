<?php

namespace App\Actions\Notification;

use App\Models\Notification;
use App\Models\NotificationFcmDelivery;

final class DispatchNotificationFcmDeliveryAction
{
    public function __construct(
        private readonly CreateNotificationFcmDeliveryAction $createDelivery,
    ) {}

    public function handle(
        Notification $notification,
    ): NotificationFcmDelivery {
        return $this->createDelivery->handle($notification);
    }
}