<?php

namespace App\Data;

use App\Enums\DevicePlatform;
use Spatie\LaravelData\Data;

final class DeviceData extends Data
{
    public function __construct(
        public string $deviceIdentifier,
        public string $fcmToken,
        public DevicePlatform $platform,
        public ?string $userIdentifier = null,
        public ?string $appVersion = null,
        public ?string $osVersion = null,
        public ?string $locale = null,
        public ?string $timezone = null,
    ) {}
}