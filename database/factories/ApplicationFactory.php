<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'package_name' => 'com.example.'.fake()->unique()->slug(2),
            'platform' => 'android',
            'description' => fake()->optional()->sentence(),
            'status' => ApplicationStatus::ACTIVE,
            'firebase_project_id' => fake()->optional()->slug(),
            'api_credentials_metadata' => null,
            'current_version' => '1.0.0',
            'current_build_number' => 1,
        ];
    }
}
