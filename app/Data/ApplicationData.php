<?php

namespace App\Data;

use Spatie\LaravelData\Data;

final class ApplicationData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $packageName,
        public string $platform = 'android',
        public ?string $description = null,
        public ?string $firebaseProjectId = null,
        public ?string $currentVersion = null,
        public ?int $currentBuildNumber = null,
    ) {}
}
