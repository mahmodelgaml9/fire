// ===== Service Worker for Caching =====
const CACHE_NAME = 'sphinx-fire-v1';
const STATIC_CACHE = 'sphinx-fire-static-v1';
const DYNAMIC_CACHE = 'sphinx-fire-dynamic-v1';

// Resources to cache immediately
const STATIC_ASSETS = [
    '/',
    '/index.html',
    '/services.html',
    '/about.html',
    '/contact.html',
    '/blog.html',
    '/projects.html',
    '/scripts/main.js',
    '/scripts/services.js',
    '/scripts/about.js',
    '/scripts/contact.js',
    '/scripts/blog.js',
    '/scripts/projects.js',
    '/performance-config.js',
    'https://cdn.tailwindcss.com',
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Service Worker: Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Service Worker: Static assets cached');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Service Worker: Error caching static assets', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Handle different types of requests
    if (isStaticAsset(request.url)) {
        event.respondWith(cacheFirst(request));
    } else if (isImage(request.url)) {
        event.respondWith(cacheFirst(request));
    } else if (isHTML(request.url)) {
        event.respondWith(networkFirst(request));
    } else {
        event.respondWith(cacheFirst(request));
    }
});

// Cache first strategy (for static assets)
async function cacheFirst(request) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('Cache first strategy failed:', error);
        return new Response('Offline content not available', { status: 503 });
    }
}

// Network first strategy (for HTML pages)
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('Network failed, trying cache:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return new Response('Page not available offline', { status: 503 });
    }
}

// Helper functions
function isStaticAsset(url) {
    return url.includes('.css') || 
           url.includes('.js') || 
           url.includes('fonts.googleapis.com') ||
           url.includes('cdnjs.cloudflare.com') ||
           url.includes('cdn.tailwindcss.com');
}

function isImage(url) {
    return url.includes('.jpg') || 
           url.includes('.jpeg') || 
           url.includes('.png') || 
           url.includes('.gif') || 
           url.includes('.webp') ||
           url.includes('images.pexels.com');
}

function isHTML(url) {
    return url.includes('.html') || 
           (!url.includes('.') && !url.includes('api'));
}

// Background sync for analytics
self.addEventListener('sync', event => {
    if (event.tag === 'analytics-sync') {
        event.waitUntil(syncAnalytics());
    }
});

async function syncAnalytics() {
    // Sync offline analytics data when online
    console.log('Service Worker: Syncing analytics data');
}

// Push notifications (for future use)
self.addEventListener('push', event => {
    if (event.data) {
        const data = event.data.json();
        
        const options = {
            body: data.body,
            icon: '/icon-192x192.png',
            badge: '/badge-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: data.primaryKey
            },
            actions: [
                {
                    action: 'explore',
                    title: 'View Details',
                    icon: '/icon-explore.png'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/icon-close.png'
                }
            ]
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/services.html')
        );
    }
});

// Message handler for communication with main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});

console.log('Service Worker: Loaded');