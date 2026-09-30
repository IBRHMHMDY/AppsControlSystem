<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

final class FirebaseService
{
    public function __construct(
        private readonly Messaging $messaging,
    ) {}

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = [],
    ): array {
        $message = CloudMessage::new()
            ->toToken($token)
            ->withNotification(
                Notification::create(
                    $title,
                    $body,
                ),
            )
            ->withData($this->normalizeData($data));

        return $this->messaging->send($message);
    }

    /**
     * @param array<string, mixed> $data
     *
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
}