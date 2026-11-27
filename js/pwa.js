// 모바일(터치 기반) 기기에서만 PWA 활성화: manifest 연결 + 서비스워커 등록.
// PC 브라우저에서는 아무것도 하지 않으므로 기존과 동일하게 동작한다.
// <script data-manifest="..."> 로 manifest 경로를 바꿀 수 있다 (기본: /manifest.json).
(function () {
  const manifest = (document.currentScript && document.currentScript.dataset.manifest) || '/manifest.json';
  const isMobile = window.matchMedia('(pointer: coarse)').matches
    || /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
  if (!isMobile) return;

  const link = document.createElement('link');
  link.rel = 'manifest';
  link.href = manifest;
  document.head.appendChild(link);

  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js').catch((e) => console.warn('SW 등록 실패', e));
    });
  }
})();
