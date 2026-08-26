<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnhancedAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::channel('security')->warning('Unauthenticated access attempt', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);

            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check role if specified
        if ($role && !$user->hasRole($role)) {
            Log::channel('security')->warning('Role-based access denied', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_role' => $role,
                'user_roles' => $user->getRoleNames()->toArray(),
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);

            abort(403, 'Acceso denegado. No tienes permisos para acceder a este recurso.');
        }

        // Log successful authentication for sensitive routes
        if ($this->isSensitiveRoute($request)) {
            Log::channel('security')->info('Authenticated access to sensitive route', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_roles' => $user->getRoleNames()->toArray(),
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'timestamp' => now(),
            ]);
        }

        return $next($request);
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
