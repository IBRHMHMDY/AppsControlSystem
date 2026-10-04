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
    case COMPLETED = 'completed';

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }
}