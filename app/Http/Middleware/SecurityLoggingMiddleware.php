<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log unauthorized access attempts
        if ($response->getStatusCode() === 403) {
            Log::channel('security')->warning('Unauthorized access attempt', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);
        }

        // Log successful access to sensitive resources
        if ($response->getStatusCode() === 200 && $this->isSensitiveRoute($request)) {
            Log::channel('security')->info('Access to sensitive resource', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->email,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if the route is considered sensitive.
     */
    private function isSensitiveRoute(Request $request): bool
    {
        $sensitiveRoutes = [
            'admin.*',
            'trabajador.*',
            '*.orders.*',
            '*.cart.processOrder',
            'profile.*',
        ];

        $routeName = $request->route()?->getName();

        if (!$routeName) {
            return false;
        }

        foreach ($sensitiveRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }
}
