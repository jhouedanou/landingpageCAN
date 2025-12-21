self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Removed empty fetch handler to avoid "no-op" warning
// Network requests will be handled normally by the browser
