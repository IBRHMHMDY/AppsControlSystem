<?php

namespace App\Actions\Device;

use App\Models\Device;

final class RefreshDeviceFcmTokenAction
{
    public function handle(
        Device $device,
        string $fcmToken,
    ): Device {
        $device->update([
            'fcm_token' => $fcmToken,
            'is_active' => true,
            'last_seen_at' => now(),
        ]);

        return $device->refresh();
    }
}