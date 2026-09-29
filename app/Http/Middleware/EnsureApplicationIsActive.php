<?php

namespace App\Http\Middleware;

use App\Enums\ApplicationStatus;
use App\Exceptions\ApiException;
use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApplicationIsActive
{
    public function handle(
        Request $request,
        Closure $next,
    ): Response {
        $application = $request->user();

        if (! $application instanceof Application) {
            throw new ApiException(
                message: 'Invalid application authentication context.',
                status: 401,
            );
        }

        if (
            $application->trashed()
            || $application->status !== ApplicationStatus::ACTIVE
        ) {
            throw new ApiException(
                message: 'Application is inactive.',
                status: 403,
            );
        }

        return $next($request);
    }
}