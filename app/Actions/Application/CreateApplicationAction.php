<?php

namespace App\Actions\Application;

use App\Data\ApplicationData;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class CreateApplicationAction
{
    public function handle(ApplicationData $data): Application
    {
        return DB::transaction(
            fn (): Application => Application::query()->create([
                'name' => $data->name,
                'slug' => $data->slug,
                'package_name' => $data->packageName,
                'platform' => $data->platform,
                'description' => $data->description,
                'status' => ApplicationStatus::ACTIVE,
                'firebase_project_id' => $data->firebaseProjectId,
                'current_version' => $data->currentVersion,
                'current_build_number' => $data->currentBuildNumber,
            ]),
        );
    }
}