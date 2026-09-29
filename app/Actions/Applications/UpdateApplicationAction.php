<?php

namespace App\Actions\Applications;

use App\Models\Application;

final class UpdateApplicationAction
{
    public function handle(
        Application $application,
        string $name,
        string $slug,
        string $packageName,
        ?string $description = null,
        ?string $firebaseProjectId = null,
        ?array $apiCredentialsMetadata = null,
        ?string $currentVersion = null,
        ?int $currentBuildNumber = null,
    ): Application {
        $application->update([
            'name' => $name,
            'slug' => $slug,
            'package_name' => $packageName,
            'description' => $description,
            'firebase_project_id' => $firebaseProjectId,
            'api_credentials_metadata' => $apiCredentialsMetadata,
            'current_version' => $currentVersion,
            'current_build_number' => $currentBuildNumber,
        ]);

        return $application->refresh();
    }
}
