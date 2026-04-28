const CACHE_NAME = 'pwa-thanhvu-cache-v1';
const urlsToCache = [
  '/',
  // Có thể thêm resource cần thiết vào đây để app báo offline vẫn load nhanh
];

// Lắng nghe sự kiện install để khởi tạo cache
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

// Kích hoạt service worker mới và dọn dẹp cache cũ
self.addEventListener('activate', event => {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName !== CACHE_NAME) {
              return caches.delete(cacheName);
            }
          })
        );
      })
    );
    self.clients.claim();
});

// Chặn request để trả về từ Cache hoặc fetch tiếp (Cache First Strategy cơ bản)
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') {
      return;
  }
  
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Dùng từ cache nếu có
        if (response) {
          return response;
        }
        // Hoặc mạng
        return fetch(event.request);
      })
  );
});
