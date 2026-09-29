<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class CreateApplicationAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function handle(array $data): Application
    {
        return DB::transaction(
            fn (): Application => Application::query()->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'package_name' => $data['package_name'],
                'platform' => $data['platform'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? ApplicationStatus::ACTIVE,
                'firebase_project_id' => $data['firebase_project_id'] ?? null,
                'api_credentials_metadata' => $data['api_credentials_metadata'] ?? null,
                'current_version' => $data['current_version'] ?? null,
                'current_build_number' => $data['current_build_number'] ?? null,
            ]),
        );
    }
}