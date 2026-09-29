<?php

namespace App\Actions\Application;

use App\Models\Application;
use Illuminate\Support\Facades\DB;

class UpdateApplicationAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function handle(Application $application, array $data): Application
    {
        return DB::transaction(function () use ($application, $data): Application {
            $application->update([
                'name' => $data['name'] ?? $application->name,
                'slug' => $data['slug'] ?? $application->slug,
                'package_name' => $data['package_name'] ?? $application->package_name,
                'platform' => $data['platform'] ?? $application->platform,
                'description' => $data['description'] ?? $application->description,
                'firebase_project_id' => $data['firebase_project_id'] ?? $application->firebase_project_id,
                'api_credentials_metadata' => array_key_exists(
                    'api_credentials_metadata',
                    $data
                )
                    ? $data['api_credentials_metadata']
                    : $application->api_credentials_metadata,
                'current_version' => $data['current_version'] ?? $application->current_version,
                'current_build_number' => $data['current_build_number'] ?? $application->current_build_number,
            ]);

            return $application->refresh();
        });
    }
}