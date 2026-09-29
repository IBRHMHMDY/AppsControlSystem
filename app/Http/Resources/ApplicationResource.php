<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'package_name' => $this->package_name,
            'platform' => $this->platform,
            'description' => $this->description,
            'status' => $this->status?->value,
            'current_version' => $this->current_version,
            'current_build_number' => $this->current_build_number,
        ];
    }
}
