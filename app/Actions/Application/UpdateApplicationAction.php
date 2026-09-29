<?php

namespace App\Actions\Application;

use App\Data\ApplicationData;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class UpdateApplicationAction
{
    public function handle(
        Application $application,
        ApplicationData $data,
    ): Application {
        return DB::transaction(function () use ($application, $data): Application {
            $application->update([
                'name' => $data->name,
                'slug' => $data->slug,
                'package_name' => $data->packageName,
                'platform' => $data->platform,
                'description' => $data->description,
                'firebase_project_id' => $data->firebaseProjectId,
                'current_version' => $data->currentVersion,
                'current_build_number' => $data->currentBuildNumber,
            ]);

            return $application->refresh();
        });
    }
}