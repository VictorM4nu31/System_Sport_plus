<?php

namespace App\Providers;

use App\Listeners\SecurityEventListener;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            SecurityEventListener::class . '@handleLogin',
        ],
        Failed::class => [
            SecurityEventListener::class . '@handleFailedLogin',
        ],
        Logout::class => [
            SecurityEventListener::class . '@handleLogout',
        ],
        Registered::class => [
            SecurityEventListener::class . '@handleRegistration',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
