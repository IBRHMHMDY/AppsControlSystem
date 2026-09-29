<?php

namespace App\Actions\Application;

use App\Data\ApplicationTokenData;
use App\Enums\ApplicationStatus;
use App\Enums\ApplicationTokenAbility;
use App\Exceptions\ApiException;
use App\Models\Application;
use Laravel\Sanctum\NewAccessToken;

final class CreateApplicationTokenAction
{
    public function handle(
        Application $application,
        ApplicationTokenData $data,
    ): NewAccessToken {
        if ($application->trashed()) {
            throw new ApiException(
                message: 'Cannot issue a token for a deleted application.',
                status: 422,
            );
        }

        if ($application->status !== ApplicationStatus::ACTIVE) {
            throw new ApiException(
                message: 'Cannot issue a token for an inactive application.',
                status: 422,
            );
        }

        $name = trim($data->name);

        if ($name === '') {
            throw new ApiException(
                message: 'Token name is required.',
                status: 422,
            );
        }

        $abilities = array_values(
            array_unique(
                $data->abilities !== []
                    ? $data->abilities
                    : [ApplicationTokenAbility::APPLICATION_READ->value],
            ),
        );

        return $application->createToken(
            name: $name,
            abilities: $abilities,
            expiresAt: $data->expiresAt,
        );
    }
}