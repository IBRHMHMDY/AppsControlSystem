<?php

namespace App\Actions\Device;

use App\Models\Device;

final class ActivateDeviceAction
{
    public function handle(Device $device): Device
    {
        $device->update([
            'is_active' => true,
        ]);

        return $device->refresh();
    }
}