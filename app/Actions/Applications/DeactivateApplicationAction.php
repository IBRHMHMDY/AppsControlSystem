<?php

namespace App\Actions\Applications;

use App\Enums\ApplicationStatus;
use App\Models\Application;

final class DeactivateApplicationAction
{
    public function handle(Application $application): Application
    {
        $application->update([
            'status' => ApplicationStatus::INACTIVE,
        ]);

        return $application->refresh();
    }
}
