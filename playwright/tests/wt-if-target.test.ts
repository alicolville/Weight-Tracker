
import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const test = base.extend<{ weightTracker: WeightTracker }>({
    weightTracker: async ({ page }, use) => {

        // Clear all weight entries and add a start weight.
        const weightTracker = new WeightTracker(page);
        await weightTracker.goto();
        await weightTracker.target_clear();
        await use(weightTracker);
    },
});

test.describe( 'wt-if target', () => {

    test.describe.configure( { mode: 'serial' } );
    
    test('missing/exists', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
        await weightTracker.goto();
        await weightTracker.target_set('50');
       
        /**
         * Check IF statements with test data
         */
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: exists');
        await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: yes');

        await weightTracker.goto();
        await weightTracker.target_clear();
    });

    test('greater than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
        await weightTracker.goto();
        await weightTracker.target_set('45');
       
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: yes');

        await weightTracker.goto();
        await weightTracker.target_set('35');
       
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-greater-40')).toContainText('Target greater than 40: no');

        await weightTracker.goto();
        await weightTracker.target_clear();
    });
    
    test('less than', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
        await weightTracker.goto();
        await weightTracker.target_set('40');
       
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-less-70')).toContainText('Target less than 70: yes');

        await weightTracker.goto();
        await weightTracker.target_set('72');

        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-less-70')).toContainText('Target less than 70: no');

        await weightTracker.goto();
        await weightTracker.target_clear();
       
    });

    test('equals', async ({ weightTracker, page }) => {
        
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-exists')).toContainText('Target Exists: missing');
        
        await weightTracker.goto();
        await weightTracker.target_set('40');
       
        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-equals-43')).toContainText('Target equals 43: no');

        await weightTracker.goto();
        await weightTracker.target_set('43');

        await page.goto('http://localhost/if-statements/if-statements-target/');
        await expect(page.locator('.wt-if-target-equals-43')).toContainText('Target equals 43: yes');

        await weightTracker.goto();
        await weightTracker.target_clear();

    });
});