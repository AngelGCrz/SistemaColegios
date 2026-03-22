const CACHE_NAME = 'sistemacolegios-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    OFFLINE_URL,
    '/manifest.json',
];

// Instalación: cachear recursos mínimos para offline
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

// Activación: limpiar caches antiguos
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            )
        )
    );
    self.clients.claim();
});

// Fetch: network-first con fallback a cache, offline page para navegación
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    // Para peticiones de navegación (HTML)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Para assets estáticos: cache-first
    if (event.request.url.match(/\.(css|js|png|jpg|jpeg|svg|ico|woff2?)$/)) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                if (cached) return cached;
                return fetch(event.request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }
});

// Push notifications (para futuro WhatsApp/notificaciones)
self.addEventListener('push', (event) => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'SistemaColegios', {
            body: data.body || '',
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-72x72.png',
            data: { url: data.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});
