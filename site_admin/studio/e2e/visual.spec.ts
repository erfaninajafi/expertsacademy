import { expect, test } from '@playwright/test';

const stamp = Date.now().toString(36);

test('the course launch preview matches its visual baseline', async ({ page }) => {
  await page.goto('page_studio.php?e2e_login=1');
  await expect(page.getByRole('heading', { name: 'Page Studio' })).toBeVisible();

  await page.getByRole('button', { name: 'Templates', exact: true }).click();
  const dialog = page.getByRole('dialog');
  await dialog.getByLabel('Search templates').fill('Course');
  await dialog.getByRole('button', { name: 'Course launch', exact: true }).click();

  const title = dialog.getByLabel('Page title');
  await title.click();
  await title.press('ControlOrMeta+a');
  await title.pressSequentially(`E2E visual ${stamp}`);
  await dialog.getByRole('button', { name: 'Create page from template' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Page created from template.');

  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.getByRole('button', { name: 'Preview' }).click();
  const frame = page.frameLocator('iframe[title="Page preview"]');
  const canvas = frame.locator('.cms-page');
  await expect(canvas).toBeVisible({ timeout: 15_000 });
  await canvas.evaluate(async () => {
    await document.fonts.ready;
    await Promise.all(
      Array.from(document.images).map((image) =>
        image.complete
          ? Promise.resolve()
          : new Promise((resolve) => {
              image.addEventListener('load', resolve, { once: true });
              image.addEventListener('error', resolve, { once: true });
            }),
      ),
    );
  });
  await page.waitForTimeout(300);

  await expect(canvas).toHaveScreenshot('course-launch.png', { animations: 'disabled', caret: 'hide' });
});
