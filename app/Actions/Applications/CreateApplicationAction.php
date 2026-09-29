<?php

namespace App\Actions\Applications;

use App\Data\ApplicationData;
use App\Enums\ApplicationStatus;
use App\Models\Application;

final class CreateApplicationAction
{
    public function handle(ApplicationData $data): Application
    {
        return Application::query()->create([
            'name' => $data->name,
            'slug' => $data->slug,
            'package_name' => $data->packageName,
            'platform' => 'android',
            'description' => $data->description,
            'status' => ApplicationStatus::ACTIVE,
            'firebase_project_id' => $data->firebaseProjectId,
            'current_version' => $data->currentVersion,
            'current_build_number' => $data->currentBuildNumber,
        ]);
    }
}
