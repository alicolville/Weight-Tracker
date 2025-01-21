import { test, expect } from '@playwright/test';

test.describe( 'License page', () => {

    test.describe.configure( { mode: 'serial' } );

    test('delete and add license', async ({ page }) => {

            await page.goto('http://localhost/wp-admin/admin.php?page=ws-ls-license');
       
            await expect(page.locator('#ws-ls-license-type')).toContainText('Premium');
        
            await page.getByRole('link', { name: 'Remove License' }).click();

            await page.getByRole('button', { name: 'Yes' }).click();

            await page.locator('textarea[name="wt-license-key"]').click();
        
            await page.locator('textarea[name="wt-license-key"]').fill('eyJ0eXBlIjoicHJvLXBsdXMiLCJleHBpcnktZGF5cyI6MTYwMDAsInNpdGUtaGFzaCI6Ijc3OWM2OSIsImV4cGlyeS1kYXRlIjoiMjA2Ny0wNi0yOCIsImhhc2giOiJlZDI5MWE1MDg2ZDBjNGEzOGVlMTRjMmNiZmUyNTExNiJ9');
        
            await page.getByRole('button', { name: 'Apply License' }).click();

            await expect(page.locator('#ws-ls-license-type')).toContainText('Premium');
        });
});