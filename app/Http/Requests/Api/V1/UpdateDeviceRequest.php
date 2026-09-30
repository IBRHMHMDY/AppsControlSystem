<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\DevicePlatform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fcm_token' => ['required', 'string'],
            'platform' => [
                'required',
                'string',
                Rule::enum(DevicePlatform::class),
            ],
            'user_identifier' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:50'],
            'os_version' => ['nullable', 'string', 'max:50'],
            'locale' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'timezone'],
        ];
    }
}