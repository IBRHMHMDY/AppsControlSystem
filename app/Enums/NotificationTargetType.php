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

    public function label(): string
    {
        return match ($this) {
            self::DEVICE => 'Device',
            self::DEVICE_TOKEN => 'Device Token',
            self::DEVICES => 'Multiple Devices',
            self::DEVICE_SELECTION => 'Device Selection',
            self::TOPIC => 'Topic',
            self::CONDITION => 'Condition',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}