<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\Device;

class DevicePolicy
{
    public function view(Application $application, Device $device): bool
    {
        return $device->application_id === $application->id;
    }

    public function update(Application $application, Device $device): bool
    {
        return $device->application_id === $application->id;
    }

    public function delete(Application $application, Device $device): bool
    {
        return false;
    }
}