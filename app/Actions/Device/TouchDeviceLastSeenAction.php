<?php

namespace App\Actions\Device;

use App\Models\Device;

final class TouchDeviceLastSeenAction
{
    public function handle(Device $device): Device
    {
        $device->update([
            'last_seen_at' => now(),
            'is_active' => true,
        ]);

        return $device->refresh();
    }
}