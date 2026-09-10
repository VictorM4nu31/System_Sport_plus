<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InputSanitizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Sanitize input data for critical routes
        if ($this->shouldSanitize($request)) {
            $this->sanitizeRequest($request);
        }

        return $next($request);
    }

    /**
     * Determine if the request should be sanitized
     */
    private function shouldSanitize(Request $request): bool
    {
        $criticalRoutes = [
            'usuario.carrito.agregar',
            'usuario.resenas.guardar',
            'usuario.direcciones.guardar',
            'usuario.direcciones.actualizar',
            'trabajador.pedidos.rechazar',
            'admin.productos.store',
            'admin.productos.update',
            'admin.categorias.store',
            'admin.categorias.update',
            'admin.trabajadores.store',
            'admin.trabajadores.update',
        ];

        return in_array($request->route()?->getName(), $criticalRoutes);
    }

    /**
     * Sanitize request input
     */
    private function sanitizeRequest(Request $request): void
    {
        $input = $request->all();
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Basic sanitization
                $value = trim($value);
                $value = strip_tags($value, '<p><br><strong><em>'); // Allow basic HTML tags
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $value = $this->sanitizeArray($value);
            }

            $sanitized[$key] = $value;
        }

        $request->replace($sanitized);
    }

    /**
     * Recursively sanitize array values
     */
    private function sanitizeArray(array $array): array
    {
        $sanitized = [];

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $value = trim($value);
                $value = strip_tags($value, '<p><br><strong><em>');
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $value = $this->sanitizeArray($value);
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }
}
