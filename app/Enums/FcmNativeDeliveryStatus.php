<?php

namespace App\Enums;

enum FcmNativeDeliveryStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::PROCESSING => 'Processing',
            self::SENT => 'Sent',
            self::FAILED => 'Failed',
        };
    }
}