<?php

namespace App\Services;

use App\Data\NotificationData;
use App\Enums\NotificationTargetType;
use InvalidArgumentException;

final class NotificationComposer
{
    /**
     * @param array<string, mixed> $dataPayload
     */
    public function compose(
        int $applicationId,
        string $title,
        string $body,
        NotificationTargetType $targetType,
        ?string $targetValue = null,
        ?string $image = null,
        array $dataPayload = [],
        ?\DateTimeInterface $scheduledAt = null,
        ?int $createdBy = null,
    ): NotificationData {
        $this->validateTarget(
            targetType: $targetType,
            targetValue: $targetValue,
        );

        return new NotificationData(
            applicationId: $applicationId,
            title: $title,
            body: $body,
            image: $image,
            dataPayload: $dataPayload,
            targetType: $targetType,
            targetValue: $targetValue,
            scheduledAt: $scheduledAt,
            createdBy: $createdBy,
        );
    }

    private function validateTarget(
        NotificationTargetType $targetType,
        ?string $targetValue,
    ): void {
        $requiresValue = match ($targetType) {
            NotificationTargetType::DEVICE,
            NotificationTargetType::DEVICE_TOKEN,
            NotificationTargetType::DEVICES,
            NotificationTargetType::DEVICE_SELECTION,
            NotificationTargetType::TOPIC,
            NotificationTargetType::CONDITION => true,
        };

        if ($requiresValue && blank($targetValue)) {
            throw new InvalidArgumentException(
                'A target value is required for the selected notification target type.',
            );
        }
    }
}