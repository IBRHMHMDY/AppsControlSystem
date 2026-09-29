<?php

namespace App\Data;

use Spatie\LaravelData\Data;

final class ApplicationTokenData extends Data
{
    /**
     * @param list<string> $abilities
     */
    public function __construct(
        public string $name,
        public array $abilities,
        public ?\DateTimeInterface $expiresAt = null,
    ) {}
}