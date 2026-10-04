<?php

namespace App\Actions\Notification;

use App\Enums\NotificationTargetType;
use App\Models\Device;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final class ResolveNotificationDevicesAction
{
    /**
     * @return Collection<int, Device>
     */
    public function handle(Notification $notification): Collection
    {
        return match ($notification->target_type) {
            NotificationTargetType::DEVICE => $this->resolveDevice(
                $notification,
            ),

            NotificationTargetType::DEVICES => $this->resolveDevices(
                $notification,
            ),

            NotificationTargetType::DEVICE_SELECTION => $this->resolveDeviceSelection(
                $notification,
            ),

            NotificationTargetType::DEVICE_TOKEN => $this->resolveDeviceToken(
                $notification,
            ),

            default => throw ValidationException::withMessages([
                'target_type' => 'This notification target type is not supported by device delivery.',
            ]),
        };
    }

    /**
     * @return Collection<int, Device>
     */
    private function resolveDevice(Notification $notification): Collection
    {
        $device = Device::query()
            ->where('id', $notification->target_value)
            ->where('application_id', $notification->application_id)
            ->where('is_active', true)
            ->first();

        if (! $device) {
            throw ValidationException::withMessages([
                'target_value' => 'The selected device is invalid or inactive.',
            ]);
        }

        return new Collection([$device]);
    }

    /**
     * @return Collection<int, Device>
     */
    private function resolveDevices(Notification $notification): Collection
    {
        $deviceIds = $this->decodeTargetValue($notification);

        $devices = Device::query()
            ->where('application_id', $notification->application_id)
            ->where('is_active', true)
            ->whereIn('id', $deviceIds)
            ->get();

        if ($devices->isEmpty()) {
            throw ValidationException::withMessages([
                'target_value' => 'No active devices were found for this notification.',
            ]);
        }

        return $devices;
    }

    /**
     * @return Collection<int, Device>
     */
    private function resolveDeviceSelection(Notification $notification): Collection
    {
        return $this->resolveDevices($notification);
    }

    /**
     * @return array<int, int>
     */
    private function decodeTargetValue(Notification $notification): array
    {
        $value = $notification->target_value;

        if (is_array($value)) {
            return array_map('intval', $value);
        }

        $decoded = json_decode((string) $value, true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'target_value' => 'The notification target value must contain device IDs.',
            ]);
        }

        return array_map('intval', $decoded);
    }

    /**
     * @return Collection<int, Device>
     */
    private function resolveDeviceToken(Notification $notification): Collection
    {
        $device = Device::query()
            ->where('application_id', $notification->application_id)
            ->where('fcm_token', $notification->target_value)
            ->where('is_active', true)
            ->first();

        if (! $device) {
            throw ValidationException::withMessages([
                'target_value' => 'The selected device token is invalid, inactive, or not registered for this application.',
            ]);
        }

        return new Collection([$device]);
    }
}
