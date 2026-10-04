<?php

namespace App\Actions\Device;

use App\Models\Device;

final class DeactivateInvalidFcmTokenAction
{
    public function handle(Device $device): void
    {
        if (! $device->is_active) {
            return;
        }

        $device->update([
            'is_active' => false,
        ]);
    }
}