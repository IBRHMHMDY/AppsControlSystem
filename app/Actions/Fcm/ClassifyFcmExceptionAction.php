<?php

namespace App\Actions\Fcm;

use App\Enums\NotificationDeliveryStatus;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\SenderIdMismatch;
use Throwable;

final class ClassifyFcmExceptionAction
{
    public function handle(Throwable $exception): NotificationDeliveryStatus
    {
        return match (true) {
            $exception instanceof NotFound,
            $exception instanceof SenderIdMismatch
                => NotificationDeliveryStatus::INVALID_TOKEN,

            default
                => NotificationDeliveryStatus::RETRYING,
        };
    }
}