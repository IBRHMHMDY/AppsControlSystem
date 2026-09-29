<?php

namespace App\Actions\Application;

use App\Models\Application;
use Illuminate\Support\Facades\DB;

class DeleteApplicationAction
{
    public function handle(Application $application): bool
    {
        return DB::transaction(
            fn (): bool => $application->delete(),
        );
    }
}