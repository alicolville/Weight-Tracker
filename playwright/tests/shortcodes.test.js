import { test, expect } from '@playwright/test';

test('Shortcode: [wt] Exists', async ({ page }) => {
  await page.goto('http://localhost/weight-tracker/');
 
  await expect(page).toHaveTitle(/Weight Tracker/);

  // If the following is true, can assume shortcode is rendering when logged in
  await expect(page.getByRole('link', { name: 'VIEW IN TABULAR FORMAT' })).toBeVisible();
});

test('Shortcode: [wt-kiosk] Exists', async ({ page }) => {
  await page.goto('http://localhost/kiosk/?wt-user-id=1');
 
  await expect(page).toHaveTitle(/Kiosk/);

   // If the following is true, can assume shortcode is rendering when logged in
  await expect(page.getByText('Editing: admin')).toBeVisible()
});