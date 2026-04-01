/**
 * FitPass Dakar — Service Worker
 * Stratégies :
 *   - Cache-first  : assets Vite (/build/*) + Google Fonts
 *   - Network-first: pages Laravel + API
 *   - Fallback     : /offline si réseau indisponible et page non cachée
 */

const CACHE_VERSION    = 'v1';
const CACHE_ASSETS     = `fitpass-${CACHE_VERSION}-assets`;
const CACHE_PAGES      = `fitpass-${CACHE_VERSION}-pages`;

// Assets pré-cachés à l'installation
const PRECACHE_ASSETS = [
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/apple-touch-icon.png',
];

// Pages pré-cachées à l'installation
const PRECACHE_PAGES = [
    '/offline',
];

// ─── Installation ────────────────────────────────────────────────────────────

self.addEventListener('install', (event) => {
    event.waitUntil(
        Promise.all([
            caches.open(CACHE_ASSETS).then((cache) => cache.addAll(PRECACHE_ASSETS)),
            caches.open(CACHE_PAGES).then((cache) => cache.addAll(PRECACHE_PAGES)),
        ]).then(() => self.skipWaiting())
    );
});

// ─── Activation (nettoyage des anciens caches) ────────────────────────────────

self.addEventListener('activate', (event) => {
    const validCaches = [CACHE_ASSETS, CACHE_PAGES];

    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => !validCaches.includes(key))
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// ─── Fetch : routage des stratégies ──────────────────────────────────────────

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorer les requêtes non-GET et les extensions navigateur
    if (request.method !== 'GET') return;
    if (!url.protocol.startsWith('http')) return;

    // Ignorer les routes admin et les webhooks PayTech
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api/webhooks')) return;

    // Cache-first : assets Vite (/build/*)
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(cacheFirst(request, CACHE_ASSETS));
        return;
    }

    // Cache-first : Google Fonts (CSS + polices)
    if (
        url.hostname === 'fonts.googleapis.com' ||
        url.hostname === 'fonts.gstatic.com'
    ) {
        event.respondWith(cacheFirst(request, CACHE_ASSETS));
        return;
    }

    // Cache-first : icônes et manifest
    if (
        url.pathname.startsWith('/icons/') ||
        url.pathname === '/manifest.json' ||
        url.pathname === '/favicon.ico'
    ) {
        event.respondWith(cacheFirst(request, CACHE_ASSETS));
        return;
    }

    // Network-first : pages Laravel (HTML) + API
    if (request.headers.get('accept')?.includes('text/html') || url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(request, CACHE_PAGES));
        return;
    }
});

// ─── Stratégie Cache-first ────────────────────────────────────────────────────

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        // Ressource statique indisponible — retourner ce qu'on a (ou rien)
        return new Response('', { status: 503, statusText: 'Service Unavailable' });
    }
}

// ─── Stratégie Network-first ──────────────────────────────────────────────────

async function networkFirst(request, cacheName) {
    try {
        const response = await fetch(request);

        // Mettre en cache uniquement les réponses HTML valides (pas les redirects, erreurs)
        if (response.ok && request.headers.get('accept')?.includes('text/html')) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }

        return response;
    } catch {
        // Réseau indisponible — chercher dans le cache
        const cached = await caches.match(request);
        if (cached) return cached;

        // Rien en cache — page /offline
        const offlinePage = await caches.match('/offline');
        return offlinePage || new Response('<h1>Hors ligne</h1>', {
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' },
        });
    }
}
