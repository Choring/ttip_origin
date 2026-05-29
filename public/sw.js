const CACHE_VERSION = 'v1';
const CACHE_NAME = `ttip-${CACHE_VERSION}`;

const PRECACHE_URLS = [
  '/offline.html',
];

// ─── Install ───────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
  );
});

// ─── Activate ──────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key.startsWith('ttip-') && key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

// ─── Fetch ─────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
  // GET 요청만 처리
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // 외부 도메인 요청은 패스 (Kakao SDK, AdSense 등)
  if (url.origin !== self.location.origin) return;

  // API, 이미지 업로드 등은 항상 네트워크
  if (
    url.pathname.startsWith('/api/') ||
    url.pathname.startsWith('/sanctum/') ||
    url.pathname.startsWith('/livewire/')
  ) return;

  // Sitemap / robots.txt / rss 는 항상 네트워크
  if (
    url.pathname.endsWith('.xml') ||
    url.pathname === '/robots.txt' ||
    url.pathname === '/rss.xml'
  ) return;

  // ── 내비게이션(HTML 페이지) → Network First ──────────────────────────────
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request)
        .catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // ── 정적 자산(JS/CSS/이미지/폰트) → Cache First ──────────────────────────
  const isStaticAsset =
    url.pathname.match(/\.(js|css|woff2?|ttf|eot|otf|svg|png|jpg|jpeg|webp|gif|ico)$/i);

  if (isStaticAsset) {
    event.respondWith(
      caches.match(event.request).then((cached) => {
        if (cached) return cached;

        return fetch(event.request).then((response) => {
          if (response && response.status === 200 && response.type === 'basic') {
            const cloned = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
          }
          return response;
        }).catch(() => new Response('', { status: 408 }));
      })
    );
    return;
  }

  // 그 외 요청은 네트워크 우선
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
