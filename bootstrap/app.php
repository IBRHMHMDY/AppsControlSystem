<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\EnsureApplicationContext;
use App\Http\Middleware\EnsureApplicationIsActive;
use App\Support\Api\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->throttleWithRedis();

        $middleware->alias([
            'application.active' => EnsureApplicationIsActive::class,
            'application.context' => EnsureApplicationContext::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            ApiException $exception
        ) {
            return $exception->render();
        });

        $exceptions->render(function (
            AuthenticationException $exception,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Unauthenticated.',
                    status: 401,
                );
            }
        });

        $exceptions->render(function (
            MissingAbilityException $exception,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'The API token does not have the required ability.',
                    status: 403,
                );
            }
        });

        $exceptions->render(function (
            TooManyRequestsHttpException $exception,
            Request $request,
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Too many requests.',
                    status: 429,
                )->withHeaders(
                    $exception->getHeaders(),
                );
            }
        });
    })->create();
