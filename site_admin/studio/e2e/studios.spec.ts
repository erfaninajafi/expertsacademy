import { expect, test } from '@playwright/test';

test.beforeEach(async ({ page }) => {
  await page.goto('page_studio.php?e2e_login=1');
  await expect(page.locator('#ea-studio-root')).toBeVisible();
});

test('design system manager renders a live token preview', async ({ page }) => {
  await page.getByRole('button', { name: 'Design system' }).click();
  await expect(page.getByRole('heading', { name: 'Design system' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Live token preview' })).toBeVisible();
  await expect(page.getByText('Contrast checks')).toBeVisible();

  const frame = page.frameLocator('iframe[title="Design token preview"]');
  await expect(frame.locator('.cms-page')).toBeVisible({ timeout: 20_000 });
  await expect(frame.locator('.cms-hero').first()).toBeVisible();
});

test('component studio previews a registry variant with the PHP renderer', async ({ page }) => {
  await page.getByRole('button', { name: 'Component Studio' }).click();
  await expect(page.getByRole('heading', { name: 'Component Studio' })).toBeVisible();

  await page.getByRole('button', { name: /Cinematic hero/ }).first().click();
  await expect(page.getByRole('heading', { name: 'Cinematic hero', level: 3 })).toBeVisible();
  await expect(page.getByText('Same PHP renderer and stylesheet as the published site')).toBeVisible();

  const frame = page.frameLocator('iframe[title="Component preview"]');
  await expect(frame.locator('.cms-hcin')).toBeVisible({ timeout: 20_000 });

  await page.getByRole('button', { name: 'RTL', exact: true }).click();
  await expect(frame.locator('.cms-page')).toHaveAttribute('dir', 'rtl');
});

test('gallery lists every registered variant', async ({ page }) => {
  await page.goto('studio_gallery.php');
  await expect(page.locator('h1')).toContainText('Component gallery');
  await expect(page.locator('main iframe')).toHaveCount(200);
  await expect(page.getByRole('heading', { name: /Cinematic hero/ })).toBeVisible();
  await expect(page.getByRole('heading', { name: /Pricing/ })).toBeVisible();
});
