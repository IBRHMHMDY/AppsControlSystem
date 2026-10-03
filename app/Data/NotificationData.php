<?php

namespace App\Data;

use App\Enums\NotificationTargetType;
use Spatie\LaravelData\Data;

final class NotificationData extends Data
{
    /**
     * @param array<string, mixed> $dataPayload
     */
    public function __construct(
        public int $applicationId,
        public string $title,
        public string $body,
        public ?string $image,
        public array $dataPayload,
        public NotificationTargetType $targetType,
        public ?string $targetValue,
        public ?\DateTimeInterface $scheduledAt,
        public ?int $createdBy,
    ) {}
}