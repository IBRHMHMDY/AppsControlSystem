<?php

namespace App\Enums;

enum ApplicationTokenAbility: string
{
    case APPLICATION_READ = 'application:read';
    case DEVICE_REGISTER = 'device:register';
    case DEVICE_UPDATE = 'device:update';
    case UPDATE_CHECK = 'update:check';
    case ADS_READ = 'ads:read';
    case NOTIFICATIONS_READ = 'notifications:read';

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }
}