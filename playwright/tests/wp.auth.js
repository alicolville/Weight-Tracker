
import { test as setup, expect } from '@playwright/test';

const authFile = '.auth/user.json';

setup('WP: Authenticate', async ({ page }) => {
  
  await page.goto('http://localhost/wp-login.php');
  await page.getByLabel('Username or Email Address').fill('admin');
  await page.locator('#user_pass').fill('password');
  await page.getByRole('button', { name: 'Log In' }).click();
  await page.waitForURL('http://localhost/wp-admin/');

  await expect(page.getByRole('link', { name: 'Edit Profile' })).toBeHidden();
  await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();

  await page.context().storageState({ path: authFile });
});