<?php

namespace App\Actions\Device;

use App\Data\DeviceData;
use App\Models\Application;
use App\Models\Device;

final class RegisterDeviceAction
{
    public function handle(
        Application $application,
        DeviceData $data,
    ): Device {
        return $application->devices()->updateOrCreate(
            [
                'device_identifier' => $data->deviceIdentifier,
            ],
            [
                'user_identifier' => $data->userIdentifier,
                'fcm_token' => $data->fcmToken,
                'platform' => $data->platform,
                'app_version' => $data->appVersion,
                'os_version' => $data->osVersion,
                'locale' => $data->locale,
                'timezone' => $data->timezone,
                'is_active' => true,
                'last_seen_at' => now(),
            ],
        );
    }
}