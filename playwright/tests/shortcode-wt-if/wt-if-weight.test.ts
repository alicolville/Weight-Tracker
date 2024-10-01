import { test, expect } from '@playwright/test';

test.describe( 'wt-if weight', () => {

    test.describe.configure( { mode: 'serial' } );
    
    test('missing/exists', async ({ page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-exists')).toContainText('Weight Exists: exists');
        
        // await set_user_target(page, '50');
       

        // /**
        //  * Check IF statements with test data
        //  */
        // await page.goto('http://localhost/if-statements/if-statements-target/');
        // await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: exists');
        // await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: yes');

        // await clear_user_target(page);
       
        // /**
        //  * Validate clean up
        //  */
        // await page.goto('http://localhost/if-statements/if-statements-target/');

        // await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        // await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: no');
    });

    // test('greater than', async ({ page }) => {
        
    //     await page.goto('http://localhost/if-statements/if-statements-target/');
    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
    //     await set_user_target(page, '45');
       
    //     await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: yes');

    //     await set_user_target(page, '35');
       
    //     await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: no');

    //     await clear_user_target(page);
       
    //     /**
    //      * Validate clean up
    //      */
    //     await page.goto('http://localhost/if-statements/if-statements-target/');

    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
    //     await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: no');
    // });
    
    // test('less than', async ({ page }) => {
        
    //     await page.goto('http://localhost/if-statements/if-statements-target/');
    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
    //     await set_user_target(page, '40');
       
    //     await expect(page.locator('.wt-if-target-less-70')).toContainText('Target less than 70: yes');

    //     await set_user_target(page, '72');
    //     await expect(page.locator('.wt-if-target-less-70')).toContainText('Target less than 70: no');

    //     await clear_user_target(page);
       
    //     /**
    //      * Validate clean up
    //      */
    //     await page.goto('http://localhost/if-statements/if-statements-target/');

    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');

    // });

    // test('equals', async ({ page }) => {
        
    //     await page.goto('http://localhost/if-statements/if-statements-target/');
    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
    //     await set_user_target(page, '40');
       
    //     await expect(page.locator('.wt-if-target-equals-43')).toContainText('Target equals 43: no');

    //     await set_user_target(page, '43');
    //     await expect(page.locator('.wt-if-target-equals-43')).toContainText('Target equals 43: yes');

    //     await clear_user_target(page);
       
    //     /**
    //      * Validate clean up
    //      */
    //     await page.goto('http://localhost/if-statements/if-statements-target/');

    //     await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');

    // });

    // async function set_user_target(page, target){
    //     await page.goto('http://localhost/weight-tracker/');
    //     await page.getByRole('link', { name: 'Adjust' }).click();
    //     await page.getByTestId('ws-form-target').click();
    //     await page.getByTestId('ws-form-target').fill( target);
    //     await page.getByRole('button', { name: 'Set Target' }).click();
    //     await page.goto('http://localhost/if-statements/if-statements-target/');
    // }

    // async function clear_user_target(page){
    //     await page.goto('http://localhost/weight-tracker/');
    //     await page.getByTestId('wt-tab-settings').click();
    //     page.on('dialog', dialog => dialog.accept());
    //     await page.getByRole('button', { name: 'Clear Target' }).click();
    //     await page.waitForURL('http://localhost/weight-tracker/?target-cleared=true&wt-user-id=8');
    // }

});