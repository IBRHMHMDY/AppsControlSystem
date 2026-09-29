<?php

namespace App\Actions\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;

class DeactivateApplicationAction
{
    public function handle(Application $application): Application
    {
        if ($application->status !== ApplicationStatus::INACTIVE) {
            $application->update([
                'status' => ApplicationStatus::INACTIVE,
            ]);
        }

        return $application->refresh();
    }
}