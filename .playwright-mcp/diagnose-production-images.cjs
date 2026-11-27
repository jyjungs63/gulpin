const { chromium } = require('C:/Users/정진영/AppData/Local/npm-cache/_npx/381cec31605a419a/node_modules/playwright');

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: 'C:/Users/정진영/AppData/Local/Packages/Claude_pzs8sxrjxfjjc/LocalCache/Local/ms-playwright/chromium-1248/chrome-win64/chrome.exe',
  });
  try {
    const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
    const errors = [];
    const failures = [];
    const images = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('requestfailed', request => failures.push({ url: request.url(), error: request.failure()?.errorText }));
    page.on('response', response => {
      if (/welcome-images|assets\/banner\/(gp_partner_bn|bn_visit|icon_gp)/.test(response.url())) {
        images.push({ url: response.url(), status: response.status() });
      }
    });
    await page.goto('https://geulpinedu.co.kr/', { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForFunction(() => [...document.querySelectorAll('#page-main img[data-src]')].every(img => img.complete), { timeout: 15000 }).catch(() => {});
    console.log(JSON.stringify({ errors, failures, images, elements: await page.locator('#page-main img[data-src]').evaluateAll(imgs => imgs.map(img => ({ name: img.dataset.src, src: img.getAttribute('src'), currentSrc: img.currentSrc, width: img.naturalWidth, complete: img.complete }))) }, null, 2));
    await page.screenshot({ path: '.playwright-mcp/production-images-check.png' });
  } finally {
    await browser.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
