<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_id' => $this->application_id,
            'user_identifier' => $this->user_identifier,
            'platform' => $this->platform?->value,
            'device_identifier' => $this->device_identifier,
            'app_version' => $this->app_version,
            'os_version' => $this->os_version,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'is_active' => $this->is_active,
            'last_seen_at' => $this->last_seen_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}