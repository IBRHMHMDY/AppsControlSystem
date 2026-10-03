<?php

namespace App\Enums;

enum NotificationTargetType: string
{
    case DEVICE = 'device';
    case DEVICE_TOKEN = 'device_token';
    case DEVICES = 'devices';
    case DEVICE_SELECTION = 'device_selection';
    case TOPIC = 'topic';
    case CONDITION = 'condition';

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }
}