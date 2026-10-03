<?php

namespace Database\Factories;

use App\Enums\NotificationStatus;
use App\Enums\NotificationTargetType;
use App\Models\Application;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'image' => null,
            'data_payload' => [],
            'target_type' => NotificationTargetType::DEVICE_TOKEN,
            'target_value' => fake()->uuid(),
            'status' => NotificationStatus::DRAFT,
            'scheduled_at' => null,
            'sent_at' => null,
            'created_by' => User::factory(),
        ];
    }
}