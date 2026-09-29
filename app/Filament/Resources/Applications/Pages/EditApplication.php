<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Actions\Application\UpdateApplicationAction;
use App\Data\ApplicationData;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function handleRecordUpdate(
        Model $record,
        array $data,
    ): Application {
        /** @var Application $record */
        return app(UpdateApplicationAction::class)->handle(
            application: $record,
            data: new ApplicationData(
                name: $data['name'],
                slug: $data['slug'],
                packageName: $data['package_name'],
                platform: $data['platform'] ?? 'android',
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