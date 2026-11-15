import { expect, test } from '@playwright/test';

const stamp = Date.now().toString(36);

async function openStudio(page: import('@playwright/test').Page): Promise<void> {
  await page.goto('page_studio.php?e2e_login=1', { waitUntil: 'domcontentloaded' });
  await expect(page.getByRole('heading', { name: 'Page Studio' })).toBeVisible();
  await expect(page.locator('#ea-studio-root')).toBeVisible();
}

async function createPage(page: import('@playwright/test').Page, title: string): Promise<void> {
  await page.getByRole('button', { name: 'New page' }).first().click();
  const dialog = page.getByRole('dialog');
  await expect(dialog).toBeVisible();
  await dialog.getByLabel('Title').fill(title);
  await dialog.getByRole('button', { name: 'Create draft' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Draft created.');
  await expect(page.locator('#page-select')).not.toHaveValue('');
}

test.beforeEach(async ({ page }) => {
  await openStudio(page);
});

test('studio shell lists the full registry', async ({ page }) => {
  await createPage(page, 'E2E shell ' + stamp);
  const library = page.getByRole('region', { name: 'Component library' });
  await expect(library).toBeVisible();
  await expect(library.getByRole('listitem')).toHaveCount(60);
  await expect(library.getByRole('button', { name: /Hero/ })).toBeVisible();
  await expect(library.getByRole('button', { name: /Cinematic hero/ })).toBeVisible();
  await expect(library.getByRole('button', { name: /Pricing/ })).toBeVisible();
  await expect(page.getByRole('tablist', { name: 'Inspector sections' })).toBeVisible();
});

test('creating, editing and saving a draft yields a new revision', async ({ page }) => {
  const title = `E2E draft ${stamp}`;
  await createPage(page, title);

  await page.getByRole('tab', { name: 'Page' }).click();
  await page.locator('#page-title').fill(`${title} edited`);
  await page.getByRole('button', { name: 'Save changes' }).click();

  await expect(page.getByTestId('studio-status')).toContainText('Saved revision 2');
  await expect(page.getByText('Unsaved')).toHaveCount(0);

  await page.reload();
  await expect(page.locator('#page-select')).toContainText(`${title} edited`);
});

test('revision history compares and restores an earlier revision', async ({ page }) => {
  const title = `E2E history ${stamp}`;
  await createPage(page, title);

  await page.getByRole('tab', { name: 'Page' }).click();
  await page.locator('#page-title').fill(`${title} v2`);
  await page.getByRole('button', { name: 'Save changes' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Saved revision 2');

  await page.locator('#page-title').fill(`${title} v3`);
  await page.getByRole('button', { name: 'Save changes' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Saved revision 3');

  await page.getByRole('button', { name: 'History' }).click();
  const dialog = page.getByRole('dialog');
  await expect(dialog.getByRole('heading', { name: 'Revision history' })).toBeVisible();

  const revisions = dialog.getByRole('list', { name: 'Revisions' }).getByRole('button');
  await expect(revisions).toHaveCount(3);
  await revisions.last().click();
  await expect(dialog.getByText('Revision #1')).toBeVisible();
  await expect(dialog.getByText(/Page-level changes:/)).toBeVisible();

  await dialog.getByRole('button', { name: 'Restore this revision' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Restored the selected revision');
  await expect(page.locator('#page-title')).toHaveValue(title);
});

test('adding a component updates the draft and the live preview', async ({ page }) => {
  await createPage(page, `E2E block ${stamp}`);
  await page.getByRole('region', { name: 'Component library' }).getByRole('button', { name: /Rich text/ }).click();
  await expect(page.getByText('Unsaved')).toBeVisible();

  await page.getByRole('button', { name: 'Preview' }).click();
  const frame = page.frameLocator('iframe[title="Page preview"]');
  await expect(frame.locator('.cms-page')).toBeVisible({ timeout: 15_000 });
  await page.getByRole('button', { name: 'Close preview' }).click();
});

test('publishing marks the page live', async ({ page }) => {
  await createPage(page, `E2E publish ${stamp}`);
  await page.getByRole('button', { name: 'Publishing' }).click();
  const dialog = page.getByRole('dialog');
  await expect(dialog).toContainText('Publishing');
  await expect(dialog.getByRole('heading', { name: 'Preflight checks' })).toBeVisible();
  await dialog.getByRole('button', { name: 'Publish now' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Published revision');
  await expect(page.getByText('Live')).toBeVisible();
});

test('scheduling, cancelling and unpublishing update the page state', async ({ page }) => {
  await createPage(page, `E2E schedule ${stamp}`);
  await page.getByRole('button', { name: 'Publishing' }).click();
  const dialog = page.getByRole('dialog');
  await expect(dialog.getByRole('heading', { name: 'Preflight checks' })).toBeVisible();
  await dialog.getByRole('button', { name: 'Copy preview link' }).click();
  await expect(dialog.getByTestId('preview-url')).toContainText('/expertsacademy/preview_link.php?token=');
  await expect(dialog.getByRole('button', { name: 'Schedule', exact: true })).toBeEnabled();
  await dialog.getByRole('button', { name: 'Schedule', exact: true }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Scheduled for');
  await expect(page.getByTestId('page-status')).toHaveText('scheduled');
  await dialog.getByRole('button', { name: 'Unpublish' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Page unpublished');
  await expect(page.getByTestId('page-status')).toHaveText('unpublished');
});

test('administrators can review the permission matrix and roles', async ({ page }) => {
  await page.getByRole('button', { name: 'Roles', exact: true }).click();
  const dialog = page.getByRole('dialog');
  await expect(dialog.getByRole('heading', { name: 'Roles and permissions' })).toBeVisible();
  await expect(dialog.getByText('pages.publish', { exact: true })).toBeVisible();
  await expect(dialog.getByText('users.manage', { exact: true })).toBeVisible();
});

test('the template library creates a page from a starter', async ({ page }) => {
  await page.getByRole('button', { name: 'Templates', exact: true }).click();
  const dialog = page.getByRole('dialog');
  await expect(dialog.getByRole('heading', { name: 'Template library' })).toBeVisible();

  const cards = dialog.getByRole('list', { name: 'Templates' }).getByRole('listitem');
  await expect(cards).toHaveCount(7);
  await dialog.getByLabel('Search templates').fill('Course');
  await expect(cards).toHaveCount(1);

  await dialog.getByRole('button', { name: 'Course launch', exact: true }).click();
  const titleInput = dialog.getByLabel('Page title');
  await titleInput.click();
  await titleInput.press('ControlOrMeta+a');
  await titleInput.pressSequentially(`E2E template ${stamp}`);
  await expect(titleInput).toHaveValue(`E2E template ${stamp}`);
  await dialog.getByRole('button', { name: 'Create page from template' }).click();
  await expect(page.getByTestId('studio-status')).toContainText('Page created from template.');

  await page.getByRole('tab', { name: 'Page' }).click();
  await expect(page.locator('#page-title')).toHaveValue(`E2E template ${stamp}`);
  await expect(page.getByRole('region', { name: 'Component library' })).toBeVisible();
});

test('layers select, reorder and hide blocks while inline editing validates content', async ({ page }) => {
  await createPage(page, `E2E layers ${stamp}`);
  await page.getByRole('button', { name: /Add section/ }).click();
  await page.getByRole('button', { name: 'Add block' }).last().click();
  const library = page.getByRole('region', { name: 'Component library' });
  await library.getByRole('button', { name: /^Hero/ }).first().click();

  const inline = page.getByTestId('inline-editor');
  const heading = inline.getByLabel('Heading');
  await heading.fill('Inline academy heading');
  await heading.press('Tab');
  await expect(heading).toHaveValue('Inline academy heading');

  await page.getByRole('tab', { name: 'Block', exact: true }).click();
  const inspector = page.getByRole('complementary', { name: 'Inspector' });
  await expect(inspector.getByRole('textbox', { name: 'Heading', exact: true })).toHaveValue('Inline academy heading');

  await heading.fill('');
  await heading.press('Tab');
  await expect(inline.getByRole('alert')).toContainText('required');
  await expect(heading).toHaveValue('Inline academy heading');

  await library.getByRole('button', { name: /Rich text/ }).first().click();
  await page.getByRole('tab', { name: 'Layers' }).click();
  const layers = page.getByRole('region', { name: 'Layers' });
  const rows = layers.getByRole('list', { name: 'New section blocks' }).getByTestId('layer-component');
  await expect(rows).toHaveCount(2);
  await expect(rows.nth(0)).toContainText('Hero');
  await expect(rows.nth(1)).toContainText('Rich text');

  await rows.nth(1).getByRole('button', { name: /Move Rich text .* up/ }).click();
  await expect(rows.nth(0)).toContainText('Rich text');

  const desktop = rows.nth(0).getByRole('button', { name: /visible on desktop/ });
  await expect(desktop).toHaveAttribute('aria-pressed', 'true');
  await desktop.click();
  await expect(desktop).toHaveAttribute('aria-pressed', 'false');
  await expect(page.getByText('Hidden on desktop')).toBeVisible();
});

test('the media picker assigns an image and the canvas follows device widths', async ({ page }) => {
  await createPage(page, `E2E media ${stamp}`);
  await page.getByRole('button', { name: /Add section/ }).click();
  await page.getByRole('button', { name: 'Add block' }).last().click();
  const library = page.getByRole('region', { name: 'Component library' });
  await library.getByRole('button', { name: /^Hero/ }).first().click();

  await page.getByRole('button', { name: 'mobile canvas width' }).click();
  const canvas = page.getByRole('region', { name: 'Page sections' });
  await expect(canvas).toHaveAttribute('data-device', 'mobile');
  await expect(canvas).toHaveCSS('max-width', '390px');

  await page.getByRole('tab', { name: 'Layers' }).click();
  const rows = page.getByRole('region', { name: 'Layers' })
    .getByRole('list', { name: 'New section blocks' })
    .getByTestId('layer-component');
  await rows.first().getByRole('button', { name: /visible on mobile/ }).click();
  await expect(page.getByText('Hidden on mobile')).toBeVisible();

  await page.getByTestId('inline-editor').getByRole('button', { name: 'Choose image' }).click();
  const picker = page.getByRole('dialog');
  await expect(picker.getByRole('heading', { name: 'Choose an image' })).toBeVisible();
  await picker.getByLabel('Alt text').fill(`E2E pixel ${stamp}`);
  await picker.getByLabel('Upload image').setInputFiles({
    name: `pixel-${stamp}.png`,
    mimeType: 'image/png',
    buffer: Buffer.from(
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=',
      'base64',
    ),
  });
  const uploaded = picker.getByRole('button', { name: `Use pixel-${stamp}.png` });
  await expect(uploaded).toBeVisible({ timeout: 15_000 });
  await uploaded.click();
  await expect(page.getByTestId('inline-editor').getByLabel('Image media ID')).not.toHaveValue('');
  await expect(page.getByText(`E2E pixel ${stamp}`)).toHaveCount(0);
});

test('keyboard undo reverts an inline heading edit', async ({ page }) => {
  await createPage(page, `E2E undo ${stamp}`);
  await page.getByRole('button', { name: /Add section/ }).click();
  await page.getByRole('button', { name: 'Add block' }).last().click();
  await page.getByRole('region', { name: 'Component library' }).getByRole('button', { name: /^Hero/ }).first().click();

  const heading = page.getByTestId('inline-editor').getByLabel('Heading');
  await heading.fill('Keyboard undo heading');
  await heading.press('Tab');
  await expect(heading).toHaveValue('Keyboard undo heading');

  await page.getByRole('heading', { name: 'Page Studio' }).click();
  await page.keyboard.press('Control+z');
  await expect(heading).not.toHaveValue('Keyboard undo heading');
});

test('the preview endpoint sends a strict CSP and escapes markup', async ({ request }) => {
  await request.get('page_studio.php?e2e_login=1');
  const sampleResponse = await request.get('studio_api.php?resource=sample');
  expect(sampleResponse.status()).toBe(200);
  const { document } = await sampleResponse.json();
  document.sections[0].components[0].props.heading = '<script>alert(1)</script>';

  const session = await request.get('studio_api.php?resource=session');
  expect(session.status()).toBe(200);
  const { csrf } = await session.json();
  const preview = await request.post('studio_preview.php', {
    headers: { 'X-CSRF-Token': csrf },
    data: { document },
  });
  expect(preview.status()).toBe(200);
  const csp = preview.headers()['content-security-policy'] ?? '';
  const scriptDirective = csp.split(';').map((part) => part.trim()).find((part) => part.startsWith('script-src'));
  expect(scriptDirective).toBe("script-src 'self'");
  const html = await preview.text();
  expect(html).not.toContain('<script>alert(1)</script>');
  expect(html).toContain('&lt;script&gt;');
});

test('the API refuses anonymous and cross-origin writes', async ({ request }) => {
  const session = await request.get('studio_api.php?resource=session');
  expect(session.status()).toBe(401);

  const crossOrigin = await request.post('studio_api.php?resource=pages', {
    headers: { Origin: 'https://evil.example', 'X-CSRF-Token': 'forged' },
    data: { title: 'should not exist', locale: 'en' },
  });
  expect([401, 403, 419]).toContain(crossOrigin.status());
});

test('the studio root has no serious accessibility violations', async ({ page }) => {
  const { default: AxeBuilder } = await import('@axe-core/playwright');
  const results = await new AxeBuilder({ page }).include('#ea-studio-root').analyze();
  const serious = results.violations.filter((violation) => ['serious', 'critical'].includes(violation.impact ?? ''));
  expect(
    serious.map((violation) => ({
      id: violation.id,
      help: violation.help,
      nodes: violation.nodes.slice(0, 4).map((node) => `${node.target.join(' ')} :: ${node.html.slice(0, 120)}`),
    })),
  ).toEqual([]);
});
