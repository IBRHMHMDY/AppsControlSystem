<?php

namespace App\Actions\Device;

use App\Models\Device;

final class UpdateDeviceAction
{
    public function handle(
        Device $device,
        array $data,
    ): Device {
        $device->update([
            'user_identifier' => $data['user_identifier'] ?? null,
            'platform' => $data['platform'],
            'app_version' => $data['app_version'] ?? null,
            'os_version' => $data['os_version'] ?? null,
            'locale' => $data['locale'] ?? null,
            'timezone' => $data['timezone'] ?? null,
            'is_active' => true,
        ]);

        return $device->refresh();
    }
}
