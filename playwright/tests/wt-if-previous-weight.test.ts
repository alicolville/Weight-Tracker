import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

 
const test = base.extend<{ weightTracker: WeightTracker }>({
    weightTracker: async ({ page }, use) => {

        // Clear all weight entries and add a start weight.
        const weightTracker = new WeightTracker(page);
        
        await weightTracker.weight_set_defaults();
        await use(weightTracker);
    },
  });

test.describe( 'wt-if previous weight', () => {

    test.describe.configure( { mode: 'serial' } );
    
    test('missing/exists', async ({ weightTracker, page }) => {
  
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.exists')).toContainText('exists: missing');
        
        await weightTracker.weight_add( '01/01/2024', '300' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.exists')).toContainText('exists: exists');

        await weightTracker.weight_clear_all();
    
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.exists')).toContainText('exists: missing');

       await weightTracker.weight_set_defaults();

    });

    test('greater than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.exists')).toContainText('exists: missing');

        await weightTracker.weight_add( '01/01/2024', '300' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
       
        await weightTracker.weight_add( '01/02/2024', '30' );
        await weightTracker.weight_add( '01/03/2024', '400' );
       
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than')).toContainText('greater than: no');

        await weightTracker.weight_add( '01/04/2024', '80' );
       
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');

        await weightTracker.weight_add( '01/05/2024', '22' );
       
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than')).toContainText('greater than: no');
        
        await weightTracker.weight_set_defaults();

    });

    test('greater than or equal to', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal: no');
    
        await weightTracker.weight_add( '01/02/2024', '233' );
        await weightTracker.weight_add( '01/03/2024', '400' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal: yes');

        await weightTracker.weight_add( '01/04/2024', '30' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal: yes');

        await weightTracker.weight_add( '01/05/2024', '234' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal: no');

        await weightTracker.weight_set_defaults();

    });

    test('less than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than')).toContainText('less than: no');
       
        await weightTracker.weight_add( '01/02/2024', '23' );
        await weightTracker.weight_add( '01/03/2024', '30' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than')).toContainText('less than: yes');

        await weightTracker.weight_add( '01/04/2024', '44' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than')).toContainText('less than: no');
      
        await weightTracker.weight_set_defaults();

    });

    test('less than or equal to', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');
       
        await weightTracker.weight_add( '01/02/2024', '13' );
        await weightTracker.weight_add( '01/03/2024', '14' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
       
        await weightTracker.weight_add( '01/04/2024', '12' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');

        await weightTracker.weight_add( '01/05/2024', '16' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');

        await weightTracker.weight_set_defaults();

    });

    test('equals', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.equals')).toContainText('equals: no');
       
        await weightTracker.weight_add( '01/02/2024', '67' );
        await weightTracker.weight_add( '01/03/2024', '14' );

        await page.goto('http://localhost/tests/if-statements/previous-weight/');
        await expect(page.locator('.equals')).toContainText('equals: yes');
        
        await weightTracker.weight_set_defaults();

    });
});