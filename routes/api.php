<?php

use App\Enums\ApplicationTokenAbility;
use App\Http\Controllers\Api\V1\ApplicationController;
use Illuminate\Support\Facades\Route;

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