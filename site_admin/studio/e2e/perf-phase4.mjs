// Lab Core Web Vitals measurement for a CMS page preview.
// Usage: node e2e/perf-phase4.mjs "http://127.0.0.1:8097/expertsacademy/preview_link.php?token=..."
import { chromium } from 'playwright';

const url = process.argv[2];
if (!url) {
  console.error('Pass the preview URL as the first argument.');
  process.exit(2);
}

const browser = await chromium.launch({ channel: 'chrome' });
const results = [];
for (let run = 0; run < 3; run++) {
  const context = await browser.newContext({ viewport: { width: 1366, height: 768 } });
  const page = await context.newPage();
  await page.addInitScript(() => {
    window.__perf = { lcp: 0, cls: 0, events: [] };
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) window.__perf.lcp = Math.max(window.__perf.lcp, entry.startTime);
    }).observe({ type: 'largest-contentful-paint', buffered: true });
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (!entry.hadRecentInput) window.__perf.cls += entry.value;
      }
    }).observe({ type: 'layout-shift', buffered: true });
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) window.__perf.events.push(entry.duration);
    }).observe({ type: 'event', buffered: true, durationThreshold: 16 });
  });
  await page.goto(url, { waitUntil: 'networkidle' });
  await page.waitForTimeout(1200);

  await page.evaluate(() => {
    document.addEventListener('pointerdown', () => performance.mark('p4-click-start'), true);
    document.addEventListener('pointerup', () => performance.measure('p4-click', 'p4-click-start'), true);
    document.addEventListener('click', () => performance.mark('p4-click-end'), true);
  });
  const button = page.locator('button').first();
  if (await button.count()) {
    await button.click({ trial: false }).catch(() => {});
  }
  await page.mouse.click(8, 8);
  await page.waitForTimeout(200);

  const metrics = await page.evaluate(() => {
    const nav = performance.getEntriesByType('navigation')[0];
    const paint = performance.getEntriesByType('paint');
    const resources = performance.getEntriesByType('resource');
    return {
      lcp: window.__perf.lcp,
      cls: window.__perf.cls,
      maxEvent: Math.max(0, ...window.__perf.events),
      interaction: performance.getEntriesByName('p4-click').at(-1)?.duration ?? 0,
      ttfb: nav ? nav.responseStart : 0,
      domContentLoaded: nav ? nav.domContentLoadedEventEnd : 0,
      load: nav ? nav.loadEventEnd : 0,
      fcp: paint.find((entry) => entry.name === 'first-contentful-paint')?.startTime ?? 0,
      resources: resources.length,
      transferBytes: resources.reduce((total, entry) => total + (entry.transferSize || 0), 0),
    };
  });
  results.push(metrics);
  await context.close();
}
await browser.close();

const median = (key) => {
  const values = results.map((entry) => entry[key]).sort((a, b) => a - b);
  return values[Math.floor(values.length / 2)];
};
const summary = {
  runs: results,
  median: {
    lcpMs: Number(median('lcp').toFixed(1)),
    cls: Number(median('cls').toFixed(4)),
    inpProxyMs: Number(median('maxEvent').toFixed(1)),
    interactionLatencyMs: Number(median('interaction').toFixed(2)),
    ttfbMs: Number(median('ttfb').toFixed(1)),
    fcpMs: Number(median('fcp').toFixed(1)),
    domContentLoadedMs: Number(median('domContentLoaded').toFixed(1)),
    loadMs: Number(median('load').toFixed(1)),
    resources: median('resources'),
    transferBytes: median('transferBytes'),
  },
};
console.log(JSON.stringify(summary, null, 2));
