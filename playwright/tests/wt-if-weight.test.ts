import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

 
const test = base.extend<{ weightTracker: WeightTracker }>({
    weightTracker: async ({ page }, use) => {

        // Clear all weight entries and add a start weight.
        const weightTracker = new WeightTracker(page);
        await weightTracker.goto();
        await weightTracker.weight_set_defaults();
        await use(weightTracker);
    },
  });

test.describe( 'wt-if weight', () => {

    test.describe.configure( { mode: 'serial' } );
    
    test('missing/exists', async ({ weightTracker, page }) => {
  
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-exists')).toContainText('Weight Exists: exists');
        
        await weightTracker.goto();
        await weightTracker.weight_clear_all();
    
        /**
         * Check IF statements with test data
         */
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-exists')).toContainText('Weight Exists: missing');

        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });

    test('greater than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-80')).toContainText('Weight greater than 80: yes');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '30' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-80')).toContainText('Weight greater than 80: no');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '81' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-80')).toContainText('Weight greater than 80: yes');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '79' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-80')).toContainText('Weight greater than 80: no');
       
        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });

    test('greater than or equal to', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-or-equal-to-233')).toContainText('Weight greater than or equal to 233: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '500' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-or-equal-to-233')).toContainText('Weight greater than or equal to 233: yes');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '30' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-or-equal-to-233')).toContainText('Weight greater than or equal to 233: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '233' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-greater-than-or-equal-to-233')).toContainText('Weight greater than or equal to 233: yes');
        
        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });

    test('less than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-30')).toContainText('Weight less than 30: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '30' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-30')).toContainText('Weight less than 30: no');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '22' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-30')).toContainText('Weight less than 30: yes');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '79' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-30')).toContainText('Weight less than 30: no');
       
        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });

    test('less than or equal to', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-or-equal-to-13')).toContainText('Weight less than or equal to 13: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '500' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-or-equal-to-13')).toContainText('Weight less than or equal to 13: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '10' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-or-equal-to-13')).toContainText('Weight less than or equal to 13: yes');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '13' );
       
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-less-than-or-equal-to-13')).toContainText('Weight less than or equal to 13: yes');
       
        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });

    test('equals', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-equals-67')).toContainText('Weight equals 67: no');
       
        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '67' );

        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-equals-67')).toContainText('Weight equals 67: yes');

        await weightTracker.goto();
        await weightTracker.weight_add( '01/10/2024', '66' );

        await page.goto('http://localhost/if-statements/if-statements-weight/');
        await expect(page.locator('.wt-if-weight-equals-67')).toContainText('Weight equals 67: no');

        await weightTracker.goto();
        await weightTracker.weight_set_defaults();

    });
});