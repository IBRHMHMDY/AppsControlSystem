<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Device\DeactivateDeviceAction;
use App\Actions\Device\RegisterDeviceAction;
use App\Actions\Device\TouchDeviceLastSeenAction;
use App\Actions\Device\UpdateDeviceAction;
use App\Data\DeviceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterDeviceRequest;
use App\Http\Requests\Api\V1\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Application;
use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    use AuthorizesRequests;

    public function store(
        RegisterDeviceRequest $request,
        Application $application,
        RegisterDeviceAction $action,
    ): DeviceResource {
        return new DeviceResource(
            $action->handle(
                $application,
                DeviceData::from($request->validated()),
            ),
        );
    }

    public function update(
        UpdateDeviceRequest $request,
        Device $device,
        UpdateDeviceAction $action,
    ): DeviceResource {
        $this->authorizeDevice($request, $device);

        return new DeviceResource(
            $action->handle(
                $device,
                DeviceData::from([
                    ...$request->validated(),
                    'deviceIdentifier' => $device->device_identifier,
                ]),
            ),
        );
    }

    public function deactivate(
        Request $request,
        Device $device,
        DeactivateDeviceAction $action,
    ): DeviceResource {
        $this->authorizeDevice($request, $device);

        return new DeviceResource(
            $action->handle($device),
        );
    }

    public function heartbeat(
        Request $request,
        Device $device,
        TouchDeviceLastSeenAction $action,
    ): DeviceResource {
        $this->authorizeDevice($request, $device);

        return new DeviceResource(
            $action->handle($device),
        );
    }

    private function authorizeDevice(
        Request $request,
        Device $device,
    ): void {
        $this->authorize('update', $device);
    }
}
