// BitSync Group Service Worker
// Provides offline functionality and caching for PWA features

const CACHE_NAME = 'bitsync-v1.0.0';
const STATIC_CACHE = 'bitsync-static-v1.0.0';
const DYNAMIC_CACHE = 'bitsync-dynamic-v1.0.0';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/index.php',
    '/pages/index.php',
    '/pages/about.php',
    '/pages/services.php',
    '/pages/solutions.php',
    '/pages/contact.php',
    '/pages/web-development.php',
    '/pages/mobile-development.php',
    '/pages/cloud-solutions.php',
    '/pages/blockchain-technology.php',
    '/pages/ai-machine-learning.php',
    '/pages/digital-transformation.php',
    '/pages/healthcare-solutions.php',
    '/pages/financial-services.php',
    '/pages/manufacturing-solutions.php',
    '/pages/retail-ecommerce.php',
    '/pages/analytics-dashboard.php',
    '/includes/header.php',
    '/includes/footer.php',
    '/includes/layout.php',
    '/includes/enhanced-chat.php',
    '/public/favicon.jpg',
    '/public/favicon-16x16.jpg',
    '/public/favicon-32x32.jpg',
    '/public/manifest.json',
    '/assets/bitsync-logo.jpg',
    '/assets/hero-tech.jpg',
    '/assets/about-office.jpg',
    '/assets/consulting-services.jpg',
    '/assets/consulting-team.jpg',
    '/assets/enterprise-solutions.jpg',
    '/assets/web-development.jpg',
    '/assets/mobile-development.jpg',
    '/assets/cloud-solutions.jpg',
    '/assets/blockchain-tech.jpg',
    '/assets/consumer-electronics.jpg',
    '/assets/system-integration.jpg',
    '/assets/why-choose-us.jpg'
];

// Install event - cache static files
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                console.log('Service Worker: Static files cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Service Worker: Error caching static files:', error);
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
                            console.log('Service Worker: Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated successfully');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip external requests
    if (url.origin !== location.origin) {
        return;
    }
    
    // Handle different types of requests
    if (request.destination === 'image') {
        event.respondWith(handleImageRequest(request));
    } else if (request.destination === 'style' || request.destination === 'script') {
        event.respondWith(handleStaticRequest(request));
    } else {
        event.respondWith(handlePageRequest(request));
    }
});

// Handle image requests
async function handleImageRequest(request) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
        console.log('Network failed for image, trying cache:', request.url);
    }
    
    // Fallback to cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    // Return a placeholder image if available
    return caches.match('/assets/bitsync-logo.jpg');
}

// Handle static requests (CSS, JS)
async function handleStaticRequest(request) {
    try {
        // Try cache first for static files
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Try network
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
        console.log('Static file not found:', request.url);
    }
    
    // Return empty response for missing static files
    return new Response('', { status: 404 });
}

// Handle page requests
async function handlePageRequest(request) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache the response
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
            return networkResponse;
        }
    } catch (error) {
        console.log('Network failed for page, trying cache:', request.url);
    }
    
    // Fallback to cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    // Return offline page
    return caches.match('/pages/offline.php');
}

// Background sync for form submissions
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    try {
        // Get pending form submissions from IndexedDB
        const pendingSubmissions = await getPendingSubmissions();
        
        for (const submission of pendingSubmissions) {
            try {
                const response = await fetch(submission.url, {
                    method: submission.method,
                    headers: submission.headers,
                    body: submission.body
                });
                
                if (response.ok) {
                    // Remove from pending submissions
                    await removePendingSubmission(submission.id);
                    console.log('Background sync successful for:', submission.url);
                }
            } catch (error) {
                console.error('Background sync failed for:', submission.url, error);
            }
        }
    } catch (error) {
        console.error('Background sync error:', error);
    }
}

// Push notification handling
self.addEventListener('push', event => {
    console.log('Push notification received:', event);
    
    const options = {
        body: event.data ? event.data.text() : 'New message from BitSync Group',
        icon: '/public/favicon.jpg',
        badge: '/public/favicon-16x16.jpg',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/public/favicon-16x16.jpg'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/public/favicon-16x16.jpg'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('BitSync Group', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    } else if (event.action === 'close') {
        // Just close the notification
        return;
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// IndexedDB helpers for background sync
async function getPendingSubmissions() {
    // This would be implemented with IndexedDB
    // For now, return empty array
    return [];
}

async function removePendingSubmission(id) {
    // This would be implemented with IndexedDB
    // For now, just log
    console.log('Removing pending submission:', id);
}

// Message handling for communication with main thread
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
}); 