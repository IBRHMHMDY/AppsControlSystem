<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
it('returns an application resource', function (): void {
    $application = Application::factory()->create([
        'status' => ApplicationStatus::ACTIVE,
    ]);

    $response = $this->getJson(
        "/api/v1/app/{$application->id}"
    );

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.id', $application->id)
        ->assertJsonPath('data.name', $application->name)
        ->assertJsonPath('data.slug', $application->slug)
        ->assertJsonPath('data.package_name', $application->package_name)
        ->assertJsonPath('data.platform', $application->platform)
        ->assertJsonPath('data.status', ApplicationStatus::ACTIVE->value);
});

it('returns not found when the application does not exist', function (): void {
    $response = $this->getJson('/api/v1/app/999999');

    $response->assertNotFound();
});