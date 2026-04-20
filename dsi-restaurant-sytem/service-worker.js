self.addEventListener('install', e => {
    console.log("🔥 Service Worker instalado");
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    console.log("✅ Service Worker activo");
});

self.addEventListener('fetch', e => {
    // modo simple (sin cache por ahora)
});