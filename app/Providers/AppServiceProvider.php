<?php

namespace App\Providers;

use App\Models\Application;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(10)
                ->by('registration:ip:'.$request->ip());
        });

        RateLimiter::for('authentication', function (Request $request) {
            return [
                Limit::perMinute(20)
                    ->by('authentication:ip:'.$request->ip()),

                Limit::perMinute(5)
                    ->by(
                        'authentication:application:'.hash(
                            'sha256',
                            (string) $request->input('application_id'),
                        ),
                    ),
            ];
        });

        RateLimiter::for('device-token', function (Request $request) {
            return [
                Limit::perMinute(60)
                    ->by('device-token:'.$this->apiRateLimitKey($request)),

                Limit::perMinute(120)
                    ->by('device-token:ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('update-check', function (Request $request) {
            return [
                Limit::perMinute(120)
                    ->by('update-check:'.$this->apiRateLimitKey($request)),
            ];
        });

        RateLimiter::for('ads', function (Request $request) {
            return [
                Limit::perMinute(120)
                    ->by('ads:'.$this->apiRateLimitKey($request)),
            ];
        });

        RateLimiter::for('notifications', function (Request $request) {
            return [
                Limit::perMinute(30)
                    ->by('notifications:'.$this->apiRateLimitKey($request)),
            ];
        });

        RateLimiter::for('application-read', function (Request $request) {
            return [
                Limit::perMinute(120)
                    ->by('application-read:'.$this->apiRateLimitKey($request)),

                Limit::perMinute(600)
                    ->by('application-read:ip:'.$request->ip()),
            ];
        });
    }

    private function apiRateLimitKey(Request $request): string
    {
        $application = $request->user();

        if ($application instanceof Application) {
            return 'application:'.$application->getKey();
        }

        $token = $request->bearerToken();

        if ($token !== null) {
            return 'token:'.hash('sha256', $token);
        }

        return 'ip:'.$request->ip();
    }
}