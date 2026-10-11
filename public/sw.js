/**
 * Service Worker - Enhanced PWA for Jabalia Municipality Field Use
 * Optimized for offline operations, field data sync, and mobile performance.
 */
const CACHE_NAME = 'jabalia-pwa-v2';
const OFFLINE_CACHE = 'jabalia-offline-v1';
const API_CACHE = 'jabalia-api-v1';

// Critical assets for field operations
const CRITICAL_ASSETS = [
    '/',
    '/dashboard',
    '/field-dashboard',
    '/css/app.css',
    '/js/app.js',
    '/images/logo.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png'
];

// Install: Pre-cache critical assets
self.addEventListener('install', function (event) {
    event.waitUntil(
        Promise.all([
            caches.open(CACHE_NAME).then(function (cache) {
                return cache.addAll(CRITICAL_ASSETS);
            }),
            caches.open(OFFLINE_CACHE).then(function (cache) {
                return cache.addAll([
                    '/offline.html',
                    '/css/offline.css',
                    '/js/offline.js'
                ]);
            })
        ]).then(function () {
            return self.skipWaiting();
        })
    );
});

// Activate: Clean up old caches and claim clients
self.addEventListener('activate', function (event) {
    event.waitUntil(
        Promise.all([
            caches.keys().then(function (keys) {
                return Promise.all(
                    keys.filter(function (key) {
                        return !key.includes('jabalia-pwa-v2') && 
                               !key.includes('jabalia-offline-v1') && 
                               !key.includes('jabalia-api-v1');
                    }).map(function (key) {
                        return caches.delete(key);
                    })
                );
            }),
            self.clients.claim()
        ])
    );
});

// Background sync for offline data
self.addEventListener('sync', function (event) {
    if (event.tag === 'field-data-sync') {
        event.waitUntil(syncOfflineData());
    }
});

// Push notifications for field updates
self.addEventListener('push', function (event) {
    const options = {
        body: event.data ? event.data.text() : 'New update available',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('Jabalia Field Update', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/field-dashboard')
        );
    } else if (event.action === 'close') {
        // Just close the notification
    } else {
        // Default action - open app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Enhanced fetch strategy for field operations
self.addEventListener('fetch', function (event) {
    const url = new URL(event.request.url);
    
    // Only handle GET requests from our origin
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // API requests: Network first with offline fallback
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(handleApiRequest(event.request));
        return;
    }

    // Field dashboard and critical pages: Network first
    if (isCriticalPage(url.pathname)) {
        event.respondWith(handleCriticalPage(event.request));
        return;
    }

    // Static assets: Cache first with network update
    if (isStaticAsset(url.pathname)) {
        event.respondWith(handleStaticAsset(event.request));
        return;
    }

    // Default: Network first
    event.respondWith(handleDefaultRequest(event.request));
});

// Handle API requests with offline support
function handleApiRequest(request) {
    return fetch(request)
        .then(function (response) {
            // Cache successful API responses
            if (response.ok) {
                const responseClone = response.clone();
                caches.open(API_CACHE).then(function (cache) {
                    cache.put(request, responseClone);
                });
            }
            return response;
        })
        .catch(function () {
            // Return cached response or offline fallback
            return caches.match(request)
                .then(function (cached) {
                    if (cached) {
                        return cached;
                    }
                    // Return offline API response
                    return new Response(
                        JSON.stringify({ 
                            error: 'Offline', 
                            message: 'No network connection',
                            cached: false 
                        }),
                        { 
                            status: 503,
                            headers: { 'Content-Type': 'application/json' }
                        }
                    );
                });
        });
}

// Handle critical pages with offline fallback
function handleCriticalPage(request) {
    return fetch(request)
        .then(function (response) {
            if (response.ok) {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(request, responseClone);
                });
                return response;
            }
            throw new Error('Network response was not ok');
        })
        .catch(function () {
            return caches.match(request)
                .then(function (cached) {
                    if (cached) {
                        return cached;
                    }
                    // Return offline page
                    return caches.match('/offline.html');
                });
        });
}

// Handle static assets with cache-first strategy
function handleStaticAsset(request) {
    return caches.match(request)
        .then(function (cached) {
            if (cached) {
                // Update cache in background
                fetch(request).then(function (response) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(function (cache) {
                        cache.put(request, responseClone);
                    });
                });
                return cached;
            }
            
            // Fetch from network
            return fetch(request)
                .then(function (response) {
                    if (response.ok) {
                        const responseClone = response.clone();
                        caches.open(CACHE_NAME).then(function (cache) {
                            cache.put(request, responseClone);
                        });
                    }
                    return response;
                });
        });
}

// Handle default requests
function handleDefaultRequest(request) {
    return fetch(request)
        .then(function (response) {
            return response;
        })
        .catch(function () {
            return caches.match(request);
        });
}

// Check if page is critical for field operations
function isCriticalPage(pathname) {
    const criticalPages = [
        '/dashboard',
        '/field-dashboard',
        '/requests',
        '/notifications',
        '/search'
    ];
    
    return criticalPages.some(page => pathname.startsWith(page));
}

// Check if request is for static asset
function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?|ttf|eot|webp|avif)(\?.*)?$/i.test(pathname);
}

// Sync offline data when back online
function syncOfflineData() {
    return caches.open(OFFLINE_CACHE).then(function (cache) {
        return cache.keys().then(function (keys) {
            return Promise.all(
                keys.map(function (request) {
                    return cache.match(request).then(function (response) {
                        if (response) {
                            const data = response.json();
                            return fetch('/api/sync-offline-data', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data)
                            }).then(function (syncResponse) {
                                if (syncResponse.ok) {
                                    // Remove synced data from offline cache
                                    return cache.delete(request);
                                }
                                return syncResponse;
                            });
                        }
                    });
                })
            );
        });
    });
}

// Periodic sync for field data
self.addEventListener('periodicsync', function (event) {
    if (event.tag === 'field-data-sync') {
        event.waitUntil(syncOfflineData());
    }
});
