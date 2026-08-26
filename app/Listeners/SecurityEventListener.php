<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SecurityEventListener
{
    /**
     * Handle user login events.
     */
    public function handleLogin(Login $event): void
    {
        Log::channel('security')->info('User login successful', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'user_roles' => $event->user->getRoleNames()->toArray(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'guard' => $event->guard,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle failed login attempts.
     */
    public function handleFailedLogin(Failed $event): void
    {
        Log::channel('security')->warning('Login attempt failed', [
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'guard' => $event->guard,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleLogout(Logout $event): void
    {
        Log::channel('security')->info('User logout', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'guard' => $event->guard,
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle user registration events.
     */
    public function handleRegistration(Registered $event): void
    {
        Log::channel('security')->info('New user registered', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Failed::class => 'handleFailedLogin',
            Logout::class => 'handleLogout',
            Registered::class => 'handleRegistration',
        ];
    }
}
