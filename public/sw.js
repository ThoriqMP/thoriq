// Thoriq Workspace - Service Worker
// Version: v1.0.0
const CACHE_NAME = 'thoriq-workspace-v1';

// Static assets to pre-cache
const PRECACHE_URLS = [
    '/',
    '/dashboard',
    '/manifest.json',
    '/pwa-192.png',
    '/pwa-512.png',
    '/favicon.ico',
];

// Install event - pre-cache key assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Pre-caching app shell...');
                return cache.addAll(PRECACHE_URLS);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => {
                        console.log('[SW] Deleting stale cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - network-first strategy for dynamic content, cache-first for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests, browser extensions, and non-same-origin requests
    if (request.method !== 'GET') return;
    if (!url.origin.startsWith(self.location.origin)) return;
    if (url.pathname.startsWith('/api/')) return;

    // Cache-first strategy for static assets (build/, images, fonts)
    const isStaticAsset = url.pathname.startsWith('/build/') ||
        url.pathname.match(/\.(png|jpg|jpeg|svg|ico|woff|woff2|ttf|css|js)$/);

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cached) => {
                return cached || fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // Network-first strategy for HTML pages (content always fresh from server)
    event.respondWith(
        fetch(request)
            .then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                // Fallback to cache when offline
                return caches.match(request)
                    .then((cached) => {
                        if (cached) return cached;
                        // Fallback to cached dashboard if no specific page found
                        return caches.match('/dashboard');
                    });
            })
    );
});

// Listen for messages from clients (e.g., force update)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
