<?php

use App\Http\Controllers\Api\V1\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('app/{application}', [ApplicationController::class, 'show'])
        ->name('api.v1.applications.show');
});