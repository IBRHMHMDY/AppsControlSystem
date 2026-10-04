<?php

namespace App\Actions\Notification;

use App\Enums\NotificationTargetType;
use App\Models\Notification;
use Illuminate\Validation\ValidationException;

final class ResolveNotificationFcmTargetAction
{
    public function handle(Notification $notification): string
    {
        return match ($notification->target_type) {
            NotificationTargetType::TOPIC => $this->resolveTopic(
                $notification,
            ),

            NotificationTargetType::CONDITION => $this->resolveCondition(
                $notification,
            ),

            default => throw ValidationException::withMessages([
                'target_type' => 'The notification target type is not an FCM native target.',
            ]),
        };
    }

    private function resolveTopic(Notification $notification): string
    {
        $topic = trim((string) $notification->target_value);

        if ($topic === '') {
            throw ValidationException::withMessages([
                'target_value' => 'The FCM topic is required.',
            ]);
        }

        return $topic;
    }

    private function resolveCondition(Notification $notification): string
    {
        $condition = trim((string) $notification->target_value);

        if ($condition === '') {
            throw ValidationException::withMessages([
                'target_value' => 'The FCM condition is required.',
            ]);
        }

        return $condition;
    }
}