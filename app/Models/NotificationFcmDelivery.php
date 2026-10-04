<?php

namespace App\Models;

use App\Enums\FcmNativeDeliveryStatus;
use App\Enums\NotificationTargetType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationFcmDelivery extends Model
{
    protected $fillable = [
        'notification_id',
        'target_type',
        'target_value',
        'target_hash',
        'status',
        'attempts',
        'error_message',
        'queued_at',
        'processing_at',
        'sent_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'target_type' => NotificationTargetType::class,
            'status' => FcmNativeDeliveryStatus::class,
            'attempts' => 'integer',
            'queued_at' => 'datetime',
            'processing_at' => 'datetime',
            'sent_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}