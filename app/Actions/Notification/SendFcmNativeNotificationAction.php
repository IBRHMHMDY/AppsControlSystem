<?php

namespace App\Actions\Notification;

use App\Enums\NotificationTargetType;
use App\Exceptions\ApiException;
use App\Models\Notification;
use App\Services\FirebaseService;

final class SendFcmNativeNotificationAction
{
    public function __construct(
        private readonly FirebaseService $firebase,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(
        Notification $notification,
        string $target,
    ): array {
        if (blank($target)) {
            throw new ApiException(
                message: 'FCM native target cannot be empty.',
                status: 422,
            );
        }

        return match ($notification->target_type) {
            NotificationTargetType::TOPIC => $this->sendToTopic(
                notification: $notification,
                target: $target,
            ),

            NotificationTargetType::CONDITION => $this->sendToCondition(
                notification: $notification,
                target: $target,
            ),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function sendToTopic(
        Notification $notification,
        string $target,
    ): array {
        return $this->firebase->sendToTopic(
            application: $notification->application,
            topic: $target,
            title: $notification->title,
            body: $notification->body,
            data: $notification->data_payload ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function sendToCondition(
        Notification $notification,
        string $target,
    ): array {
        return $this->firebase->sendToCondition(
            application: $notification->application,
            condition: $target,
            title: $notification->title,
            body: $notification->body,
            data: $notification->data_payload ?? [],
        );
    }
}