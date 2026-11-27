// 글핀 PWA 서비스워커
// 정적 파일(css/js/이미지/폰트)만 캐시하고, PHP 페이지·API·미디어(mp3/wav/mp4/pdf)는 항상 네트워크로 보낸다.
const CACHE = 'gulpin-static-v1';
const STATIC_RE = /\.(css|js|png|jpe?g|gif|svg|webp|ico|woff2?|ttf)$/i;

self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET' || req.headers.has('range')) return;
  const url = new URL(req.url);
  if (url.origin !== self.location.origin || !STATIC_RE.test(url.pathname)) return;
  if (url.pathname.startsWith('/_private/')) return;

  // stale-while-revalidate: 캐시가 있으면 즉시 반환하고 백그라운드로 갱신
  event.respondWith(
    caches.open(CACHE).then(async (cache) => {
      const cached = await cache.match(req);
      const network = fetch(req).then((res) => {
        if (res.ok) cache.put(req, res.clone());
        return res;
      }).catch(() => cached);
      return cached || network;
    })
  );
});
