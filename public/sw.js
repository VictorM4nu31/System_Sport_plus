/* Service Worker Campos Sport (v1).
 * Estrategia:
 * - Imágenes del catálogo (/img/*, /storage/products/*): cache-first.
 * - Navegaciones: network-first con fallback offline (nunca checkout).
 * - NUNCA cachear: /carrito*, /stripe/*, métodos no-GET, JSON de búsqueda.
 */
const VERSION = 'v1';
const IMAGE_CACHE = `tienda-img-${VERSION}`;

const esNavegacion = (request) => request.mode === 'navigate';
const esImagenCacheable = (url) => url.pathname.startsWith('/img/')
    || url.pathname.startsWith('/storage/products/');
const esRutaSensible = (url) => url.pathname.startsWith('/carrito')
    || url.pathname.startsWith('/stripe')
    || url.pathname.endsWith('/ficha')
    || url.pathname.endsWith('/search')
    || url.pathname.endsWith('/buscar')
    || url.pathname.endsWith('/reserva-estado');

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((nombres) => Promise.all(
            nombres.filter((n) => n !== IMAGE_CACHE).map((n) => caches.delete(n)),
        )).then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin || esRutaSensible(url)) {
        return;
    }

    if (request.destination === 'image' || esImagenCacheable(url)) {
        event.respondWith(
            caches.open(IMAGE_CACHE).then((cache) => cache.match(request).then((hit) => {
                const red = fetch(request).then((respuesta) => {
                    if (respuesta && respuesta.ok) {
                        cache.put(request, respuesta.clone());
                    }
                    return respuesta;
                }).catch(() => hit);
                return hit || red;
            })),
        );
        return;
    }

    if (esNavegacion(request)) {
        event.respondWith(
            fetch(request).catch(() => new Response(
                '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sin conexión — Campos Sport</title></head>'
                + '<body style="font-family:sans-serif;padding:2rem;text-align:center">'
                + '<h1>Sin conexión</h1><p>El catálogo necesita internet. El pago nunca se procesa sin conexión.</p>'
                + '<p><a href="/productos">Reintentar</a></p></body></html>',
                { headers: { 'Content-Type': 'text/html; charset=utf-8' } },
            )),
        );
    }
});
