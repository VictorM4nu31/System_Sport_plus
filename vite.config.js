import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Alerta si un chunk supera ~250 kB gzip (Stripe/Chart van por CDN, fuera del bundle).
        chunkSizeWarningLimit: 250,
        rollupOptions: {
            output: {
                // Vendor separado para mejor cacheo del navegador.
                manualChunks: {
                    vendor: ['alpinejs', 'axios'],
                },
            },
        },
    },
});
