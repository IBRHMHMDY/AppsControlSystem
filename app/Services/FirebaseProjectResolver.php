<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Application;

final class FirebaseProjectResolver
{
    public function resolve(Application $application): array
    {
        $projectId = $application->firebase_project_id;

        if ($projectId === null || $projectId === '') {
            throw new ApiException(
                message: 'Firebase project is not configured for this application.',
                status: 422,
            );
        }

        $project = config("firebase.projects.{$projectId}");

        if (! is_array($project)) {
            throw new ApiException(
                message: 'Firebase project configuration was not found.',
                status: 422,
            );
        }

        if (
            ! isset($project['credentials'])
            || ! is_string($project['credentials'])
            || $project['credentials'] === ''
        ) {
            throw new ApiException(
                message: 'Firebase credentials are not configured.',
                status: 422,
            );
        }

        return $project;
    }
}