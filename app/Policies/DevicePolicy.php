<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

class DevicePolicy
{
    public function view(User $user, Device $device): bool
    {
        return $device->application?->api_user_id === $user->id;
    }

    public function update(User $user, Device $device): bool
    {
        return $device->application?->api_user_id === $user->id;
    }
}