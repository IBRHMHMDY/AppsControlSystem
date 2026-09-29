<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Application $application): bool {
        return $application->api_user_id === $user->getKey()
            && $application->status->value === ApplicationStatus::ACTIVE;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Application $application): bool
    {
        return true;
    }

    public function delete(User $user, Application $application): bool
    {
        return true;
    }
}
