<?php

namespace App\Jobs\Notification;

use App\Actions\Fcm\SendFcmNotificationAction;
use App\Models\Device;
use App\Models\Notification as NotificationModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;

#[Tries(3)]
#[Backoff(3)]
#[Timeout(60)]
final class DeliverNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly NotificationModel $notification,
        public readonly Device $device,
    ) {}

    public function handle(
        SendFcmNotificationAction $action,
    ): void {
        $action->handle(
            device: $this->device,
            title: $this->notification->title,
            body: $this->notification->body,
            data: $this->notification->data_payload ?? [],
        );
    }
}