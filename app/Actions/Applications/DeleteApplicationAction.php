<?php

namespace App\Actions\Applications;

use App\Models\Application;

final class DeleteApplicationAction
{
    public function handle(Application $application): void
    {
        $application->delete();
    }
}
