import { test, expect } from '@playwright/test';

test('Page: [wt] Exists', async ({ page }) => {
  await page.goto('http://localhost/weight-tracker/');
 
  await expect(page).toHaveTitle(/Weight Tracker/);
});

test('Page: [wt-kiosk] Exists', async ({ page }) => {
  await page.goto('http://localhost/kiosk/');
 
  await expect(page).toHaveTitle(/Kiosk/);
});