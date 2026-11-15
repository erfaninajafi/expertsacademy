import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './e2e',
  fullyParallel: false,
  workers: 1,
  timeout: 45_000,
  expect: { timeout: 8_000 },
  outputDir: process.env.EA_STUDIO_TEST_OUTPUT ?? '/tmp/ea-studio-playwright-results',
  reporter: [['list']],
  use: {
    baseURL: process.env.EA_STUDIO_BASE_URL ?? 'http://127.0.0.1:8097/expertsacademy/site_admin/',
    channel: process.env.EA_STUDIO_BROWSER_CHANNEL ?? 'chrome',
    actionTimeout: 8_000,
    navigationTimeout: 15_000,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
  },
  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],
});
