<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApplicationContext
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $authenticatedApplication = $request->user();
        $routeApplication = $request->route('application');

        if (! $authenticatedApplication instanceof Application) {
            throw new ApiException(
                message: 'Invalid application context.',
                status: 403,
            );
        }

        if ($routeApplication !== null) {
            if (! $routeApplication instanceof Application) {
                throw new ApiException(
                    message: 'Invalid application context.',
                    status: 403,
                );
            }

            if (! $authenticatedApplication->is($routeApplication)) {
                throw new ApiException(
                    message: 'The authenticated application cannot access this resource.',
                    status: 403,
                );
            }

            return $next($request);
        }

        $request->attributes->set(
            'application',
            $authenticatedApplication,
        );

        return $next($request);
    }
}