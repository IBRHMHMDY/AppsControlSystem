<?php

namespace App\Enums;

enum NotificationDeliveryStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case FAILED = 'failed';
    case INVALID_TOKEN = 'invalid_token';
    case RETRYING = 'retrying';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(
                fn (self $case): array => [
                    $case->value => $case->value,
                ],
            )
            ->all();
    }
}