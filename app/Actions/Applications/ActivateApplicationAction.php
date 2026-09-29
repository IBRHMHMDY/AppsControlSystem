<?php

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;

final class ActivateApplicationAction
{
    public function handle(Application $application): Application
    {
        $application->update([
            'status' => ApplicationStatus::ACTIVE,
        ]);

        return $application->refresh();
    }
}
