<?php

namespace App\Actions\Device;

use App\Data\DeviceData;
use App\Models\Device;

final class UpdateDeviceAction
{
    public function handle(
        Device $device,
        DeviceData $data,
    ): Device {
        $device->update([
            'user_identifier' => $data->userIdentifier,
            'fcm_token' => $data->fcmToken,
            'platform' => $data->platform,
            'app_version' => $data->appVersion,
            'os_version' => $data->osVersion,
            'locale' => $data->locale,
            'timezone' => $data->timezone,
            'is_active' => true,
        ]);

        return $device->refresh();
    }
}