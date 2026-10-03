<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\NotificationTargetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'title',
        'body',
        'image',
        'data_payload',
        'target_type',
        'target_value',
        'status',
        'scheduled_at',
        'sent_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'data_payload' => 'array',
            'target_type' => NotificationTargetType::class,
            'status' => NotificationStatus::class,
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}