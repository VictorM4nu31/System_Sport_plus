<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAnalytics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo rastrear peticiones GET exitosas
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // No rastrear rutas de admin, API o assets
            if (!$this->shouldTrack($request)) {
                return $response;
            }

            try {
                $analyticsService = app(\App\Services\AnalyticsService::class);
                $userId = null;

                // Verificar si la sesión está disponible y el usuario autenticado
                if ($request->hasSession() && auth()->check()) {
                    $userId = auth()->id();
                }

                $analyticsService->trackPageView($request, $userId);
            } catch (\Exception $e) {
                // No interrumpir la aplicación si falla el tracking
                \Illuminate\Support\Facades\Log::error('Analytics tracking failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Determinar si se debe rastrear esta petición
     */
    private function shouldTrack(Request $request): bool
    {
        $path = $request->path();

        // No rastrear estas rutas
        $excludedPaths = [
            'admin/*',
            'api/*',
            'storage/*',
            'css/*',
            'js/*',
            'images/*',
            'favicon.ico',
            'robots.txt',
            'sitemap.xml',
            'login',
            'register',
            'forgot-password',
            'reset-password/*',
            'verify-email',
            'verify-email/*',
        ];

        foreach ($excludedPaths as $excludedPath) {
            if (fnmatch($excludedPath, $path)) {
                return false;
            }
        }

        return true;
    }
}
