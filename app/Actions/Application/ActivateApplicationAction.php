<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;

class ActivateApplicationAction
{
    public function handle(Application $application): Application
    {
        if ($application->trashed()) {
            $application->restore();
        }

        if ($application->status !== ApplicationStatus::ACTIVE) {
            $application->update([
                'status' => ApplicationStatus::ACTIVE,
            ]);
        }

        return $application->refresh();
    }
}