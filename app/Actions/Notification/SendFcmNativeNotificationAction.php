<?php

namespace App\Actions\Notification;

use App\Enums\NotificationTargetType;
use App\Models\Notification;
use App\Services\FirebaseService;
use InvalidArgumentException;

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
        return match ($notification->target_type) {
            NotificationTargetType::TOPIC => $this->firebase->sendToTopic(
                application: $notification->application,
                topic: $target,
                title: $notification->title,
                body: $notification->body,
                data: $notification->data_payload ?? [],
            ),

            NotificationTargetType::CONDITION => $this->firebase->sendToCondition(
                application: $notification->application,
                condition: $target,
                title: $notification->title,
                body: $notification->body,
                data: $notification->data_payload ?? [],
            ),

            default => throw new InvalidArgumentException(
                'The notification target type is not supported by FCM native delivery.',
            ),
        };
    }
}