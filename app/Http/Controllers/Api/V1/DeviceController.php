<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Device\DeactivateDeviceAction;
use App\Actions\Device\RefreshDeviceFcmTokenAction;
use App\Actions\Device\RegisterDeviceAction;
use App\Actions\Device\TouchDeviceLastSeenAction;
use App\Actions\Device\UpdateDeviceAction;
use App\Data\DeviceData;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RefreshDeviceFcmTokenRequest;
use App\Http\Requests\Api\V1\RegisterDeviceRequest;
use App\Http\Requests\Api\V1\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Application;
use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeviceController extends Controller
{
    use AuthorizesRequests;

    public function store(
        RegisterDeviceRequest $request,
        RegisterDeviceAction $action,
    ): DeviceResource {
        /** @var Application $application */
        $application = $request->attributes->get('application');

        $device = $action->handle(
            $application,
            DeviceData::from($request->validated()),
        );

        return new DeviceResource($device);
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
                $request->validated(),
            ),
        );
    }

    public function refreshFcmToken(
        RefreshDeviceFcmTokenRequest $request,
        Device $device,
        RefreshDeviceFcmTokenAction $action,
    ): DeviceResource {
        $this->authorizeDevice($request, $device);

        return new DeviceResource(
            $action->handle(
                $device,
                $request->validated('fcm_token'),
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

    private function application(Request $request): Application
    {
        /** @var Application $application */
        $application = $request->attributes->get('application');
        if ($application instanceof Application) {
            throw new ApiException(
                message: 'Application context could not be resolved.',
                status: 403,
            );
        }

        return $application;
    }

    private function authorizeDevice(
        Request $request,
        Device $device,
    ): void {
        Gate::forUser(
            $this->application($request)
        )->authorize('update', $device);
    }
}
