import { test, expect } from '@playwright/test';

test.describe( 'Admin Dashboard', () => {

    test.describe.configure( { mode: 'serial' } );
    

    test('check quick stats', async ({ page }) => {
        
        await page.goto('http://localhost/wp-admin/admin.php?page=ws-ls-data-home');
       
        var value = await page.getByTestId('wt-no-wp-users').textContent();
        expect(  Number( value ) ).toBeGreaterThanOrEqual(1);

        var value = await page.getByTestId('wt-no-weights').textContent();
        expect( Number( value ) ).toBeGreaterThanOrEqual(1);

        var value = await page.getByTestId('wt-no-targets').textContent();
        expect( Number( value ) ).toBeGreaterThanOrEqual(1);

    });
    
});