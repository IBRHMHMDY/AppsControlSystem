<?php

namespace App\Actions\Fcm;

use App\Models\Device;
use App\Services\FirebaseService;

final class SendFcmNotificationAction
{
    public function __construct(
        private readonly FirebaseService $firebase,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function handle(
        Device $device,
        string $title,
        string $body,
        array $data = [],
    ): array {
        return $this->firebase->sendToToken(
            token: $device->fcm_token,
            title: $title,
            body: $body,
            data: $data,
        );
    }
}