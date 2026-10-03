<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PENDING = 'pending';
    case SENT = 'sent';
    case CANCELLED = 'cancelled';

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }
}