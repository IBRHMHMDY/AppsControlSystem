<?php

namespace App\Actions\Application;

use App\Exceptions\ApiException;
use App\Models\Application;
use Laravel\Sanctum\PersonalAccessToken;

final class RevokeApplicationTokenAction
{
    public function handle(
        Application $application,
        PersonalAccessToken $token,
    ): void {
        if (! $application->tokens()->whereKey($token->getKey())->exists()) {
            throw new ApiException(
                message: 'The token does not belong to this application.',
                status: 403,
            );
        }

        $token->delete();
    }
}