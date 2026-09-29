<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Actions\Application\CreateApplicationAction;
use App\Data\ApplicationData;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Pages\CreateRecord;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function handleRecordCreation(array $data): Application
    {
        return app(CreateApplicationAction::class)->handle(
            new ApplicationData(
                name: $data['name'],
                slug: $data['slug'],
                packageName: $data['package_name'],
                description: $data['description'] ?? null,
                firebaseProjectId: $data['firebase_project_id'] ?? null,
                currentVersion: $data['current_version'] ?? null,
                currentBuildNumber: isset($data['current_build_number'])
                    ? (int) $data['current_build_number']
                    : null,

            ),
        );
    }
}
