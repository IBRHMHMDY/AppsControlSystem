<?php

namespace App\Actions\Device;

use App\Models\Device;

final class DeactivateDeviceAction
{
    public function handle(Device $device): Device
    {
        $device->update([
            'is_active' => false,
        ]);

        return $device->refresh();
    }
}