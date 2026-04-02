const CACHE_NAME = 'healtiva-static-v2';
const PRECACHE_URLS = ['/', '/manifest.webmanifest', '/images/logo.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Never rewrite shared report links. They must be fetched from network as-is.
    if (url.pathname.startsWith('/share/') || url.pathname.startsWith('/s/')) {
        event.respondWith(fetch(event.request));
        return;
    }

    // For navigation requests, prefer fresh network response.
    if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match('/')));
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;
            return fetch(event.request)
                .then((response) => {
                    if (!response || response.status !== 200 || response.type !== 'basic') return response;
                    const cloned = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
                    return response;
                })
                .catch(() => caches.match('/'));
        })
    );
});
