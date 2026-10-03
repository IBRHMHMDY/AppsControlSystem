<?php

namespace App\Services;

use App\Models\Application;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;

final class ApplicationFirebaseFactory
{
    public function __construct(
        private readonly FirebaseProjectResolver $resolver,
    ) {}

    public function messaging(Application $application): Messaging
    {
        $configuration = $this->resolver->resolve($application);

        return (new Factory())
            ->withServiceAccount($configuration['credentials'])
            ->createMessaging();
    }
}