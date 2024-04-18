import { test, expect } from '@playwright/test';

test('Shortcode: [wt] Exists', async ({ page }) => {
    await page.goto('http://localhost/weight-tracker/');
   
    await expect(page).toHaveTitle(/Weight Tracker/);
  
    // If the following is true, can assume shortcode is rendering when logged in
    await expect(page.getByRole('link', { name: 'VIEW IN TABULAR FORMAT' })).toBeVisible();
});

test('[wt] Add Weight', async ({ page }) => {

    await page.goto('http://localhost/weight-tracker/');

    await page.getByTestId('wt-tab-add-edit').click();
   
    await expect(page.getByText('Add a new entry')).toBeVisible()


});
