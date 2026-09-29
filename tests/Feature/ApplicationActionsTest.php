<?php

use App\Actions\Applications\ActivateApplicationAction;
use App\Actions\Applications\CreateApplicationAction;
use App\Actions\Applications\DeactivateApplicationAction;
use App\Actions\Applications\DeleteApplicationAction;
use App\Actions\Applications\UpdateApplicationAction;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an application', function () {
    $application = app(CreateApplicationAction::class)->handle(
        name: 'Test Application',
        slug: 'test-application',
        packageName: 'com.example.test',
    );

    expect($application)
        ->name->toBe('Test Application')
        ->platform->toBe('android')
        ->status->toBe(ApplicationStatus::ACTIVE);

    $this->assertDatabaseHas('applications', [
        'slug' => 'test-application',
        'package_name' => 'com.example.test',
        'platform' => 'android',
        'status' => 'active',
    ]);
});

it('updates an application', function () {
    $application = Application::factory()->create();

    $updated = app(UpdateApplicationAction::class)->handle(
        application: $application,
        name: 'Updated Application',
        slug: 'updated-application',
        packageName: 'com.example.updated',
    );

    expect($updated->name)->toBe('Updated Application')
        ->and($updated->platform)->toBe('android');
});

it('activates an application', function () {
    $application = Application::factory()->create([
        'status' => ApplicationStatus::INACTIVE,
    ]);

    app(ActivateApplicationAction::class)->handle($application);

    expect($application->refresh()->status)
        ->toBe(ApplicationStatus::ACTIVE);
});

it('deactivates an application', function () {
    $application = Application::factory()->create([
        'status' => ApplicationStatus::ACTIVE,
    ]);

    app(DeactivateApplicationAction::class)->handle($application);

    expect($application->refresh()->status)
        ->toBe(ApplicationStatus::INACTIVE);
});

it('deletes an application', function () {
    $application = Application::factory()->create();

    app(DeleteApplicationAction::class)->handle($application);

    expect(Application::find($application->id))->toBeNull();
});
