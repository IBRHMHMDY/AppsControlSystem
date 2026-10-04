<?php

namespace App\Actions\Notification;

use App\Data\NotificationData;
use App\Enums\NotificationStatus;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

final class CreateNotificationAction
{
    public function handle(NotificationData $data): Notification
    {
        return DB::transaction(
            fn (): Notification => Notification::query()->create([
                'application_id' => $data->applicationId,
                'title' => $data->title,
                'body' => $data->body,
                'image' => $data->image,
                'data_payload' => $data->dataPayload,
                'target_type' => $data->targetType,
                'target_value' => $data->targetValue,
                
                'status' => NotificationStatus::DRAFT,
                'scheduled_at' => $data->scheduledAt,
                'created_by' => $data->createdBy,
            ]),
        );
    }
}