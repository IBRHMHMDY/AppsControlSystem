<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Application;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

final class FirebaseService
{
    public function __construct(
        private readonly ApplicationFirebaseFactory $factory,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToApplicationToken(
        Application $application,
        string $token,
        string $title,
        string $body,
        array $data = [],
    ): array {
        if (! $application->isActive()) {
            throw new ApiException(
                message: 'Cannot send FCM notification for an inactive application.',
                status: 403,
            );
        }
        $message = CloudMessage::new()
            ->toToken($token)
            ->withNotification(
                Notification::create(
                    $title,
                    $body,
                ),
            )
            ->withData($this->normalizeData($data));

        return $this->factory
            ->messaging($application)
            ->send($message);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function normalizeData(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[(string) $key] = is_scalar($value)
                ? (string) $value
                : json_encode($value, JSON_THROW_ON_ERROR);
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToTopic(
        Application $application,
        string $topic,
        string $title,
        string $body,
        array $data = [],
    ): array {
        if (! $application->isActive()) {
            throw new ApiException(
                message: 'Cannot send FCM notification for an inactive application.',
                status: 403,
            );
        }

        $this->validateTarget($topic);

        $message = CloudMessage::new()
            ->toTopic($topic)
            ->withNotification(
                Notification::create(
                    $title,
                    $body,
                ),
            )
            ->withData($this->normalizeData($data));

        return $this->factory
            ->messaging($application)
            ->send($message);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToCondition(
        Application $application,
        string $condition,
        string $title,
        string $body,
        array $data = [],
    ): array {
        if (! $application->isActive()) {
            throw new ApiException(
                message: 'Cannot send FCM notification for an inactive application.',
                status: 403,
            );
        }

        $this->validateTarget($condition);

        $message = CloudMessage::new()
            ->toCondition($condition)
            ->withNotification(
                Notification::create(
                    $title,
                    $body,
                ),
            )
            ->withData($this->normalizeData($data));

        return $this->factory
            ->messaging($application)
            ->send($message);
    }

    private function validateTarget(string $target): void
    {
        if (blank($target)) {
            throw new ApiException(
                message: 'FCM target cannot be empty.',
                status: 422,
            );
        }
    }
}
