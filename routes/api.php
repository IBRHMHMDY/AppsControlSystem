<?php

use App\Enums\ApplicationTokenAbility;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\DeviceController;
use Illuminate\Support\Facades\Route;

// Applications
Route::prefix('v1')->group(function (): void {
    Route::middleware([
        'auth:sanctum',
        'application.active',
        'application.context',
        'abilities:'.ApplicationTokenAbility::APPLICATION_READ->value,
        'throttle:application-read',
    ])->group(function (): void {
        Route::get('app/{application}', [ApplicationController::class, 'show'])
            ->name('api.v1.applications.show');
    });
});
// Devices
Route::prefix('v1')->group(function (): void {
    Route::middleware([
        'auth:sanctum',
        'application.active',
        'application.context',
    ])->group(function (): void {
        Route::post('devices', [DeviceController::class, 'store'])
            ->middleware([
                'abilities:'.ApplicationTokenAbility::DEVICE_REGISTER->value,
                'throttle:device-registration',
            ])
            ->name('api.v1.devices.store');

        Route::patch('devices/{device}', [DeviceController::class, 'update'])
            ->middleware([
                'abilities:'.ApplicationTokenAbility::DEVICE_UPDATE->value,
                'throttle:device-update',
            ])
            ->name('api.v1.devices.update');

        Route::post('devices/{device}/deactivate', [DeviceController::class, 'deactivate'])
            ->middleware([
                'abilities:'.ApplicationTokenAbility::DEVICE_UPDATE->value,
                'throttle:device-update',
            ])
            ->name('api.v1.devices.deactivate');

        Route::post('devices/{device}/heartbeat', [DeviceController::class, 'heartbeat'])
            ->middleware([
                'abilities:'.ApplicationTokenAbility::DEVICE_UPDATE->value,
                'throttle:device-heartbeat',
            ])
            ->name('api.v1.devices.heartbeat');
    });
});
