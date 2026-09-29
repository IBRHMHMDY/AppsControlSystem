<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Actions\Applications\UpdateApplicationAction;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Application
    {
        return app(UpdateApplicationAction::class)->handle(
            application: $record,
            name: $data['name'],
            slug: $data['slug'],
            packageName: $data['package_name'],
            description: $data['description'] ?? null,
            firebaseProjectId: $data['firebase_project_id'] ?? null,
            apiCredentialsMetadata: null,
            currentVersion: $data['current_version'] ?? null,
            currentBuildNumber: isset($data['current_build_number'])
                ? (int) $data['current_build_number']
                : null,
        );
    }
}
