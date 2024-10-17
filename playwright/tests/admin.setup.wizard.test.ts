import { test, expect } from '@playwright/test';

test.describe( 'Setup Wizard', () => {

    test.describe.configure( { mode: 'serial' } );

    test('show and dismiss setup wizard', async ({ page }) => {

        await page.goto('http://localhost/wp-admin/admin.php?page=ws-ls-help&wlt-show-setup-wizard-links=y');
    
        await expect(page.getByRole('link', { name: 'Run wizard' })).toBeVisible();

        await page.locator('.setup-wizard-dismiss .notice-dismiss').click();
        
        await expect(page.getByRole('link', { name: 'Run wizard' })).toBeVisible( { timeout: 500, visible: false } );

        await page.goto('http://localhost/wp-admin/admin.php?page=ws-ls-help');

        await expect(page.getByRole('link', { name: 'Run wizard' })).toBeVisible( { timeout: 500, visible: false } );

    });
});
