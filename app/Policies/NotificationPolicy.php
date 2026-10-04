<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool)$user->is_admin;
    }

    public function view(User $user, Notification $notification): bool
    {
        return (bool)$user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool)$user->is_admin;
    }

    public function update(User $user, Notification $notification): bool
    {
        return (bool)$user->is_admin;
    }

    public function delete(User $user, Notification $notification): bool
    {
        return false;
    }
}