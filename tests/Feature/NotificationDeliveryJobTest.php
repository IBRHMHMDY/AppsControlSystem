<?php

use App\Jobs\Notification\DeliverNotificationJob;
use App\Models\Device;
use App\Models\Notification;
use Illuminate\Support\Facades\Queue;

it('can dispatch notification delivery job', function (): void {
    Queue::fake();

    $notification = Notification::factory()->create();

    $device = Device::query()->create([
        'application_id' => $notification->application_id,
        'fcm_token' => 'test-fcm-token',
        'platform' => 'android'
    ]);

    DeliverNotificationJob::dispatch(
        notification: $notification,
        device: $device,
    );

    Queue::assertPushed(
        DeliverNotificationJob::class,
        fn (DeliverNotificationJob $job): bool => $job->notification->is($notification)
            && $job->device->is($device),
    );
});
