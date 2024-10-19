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

test.describe.configure( { mode: 'serial' } );

test('wt-if difference from start', async ({ weightTracker, page }) => {
    
    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: no');
    await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
    await expect(page.locator('.less-than')).toContainText('less than: no');
    await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');
    await expect(page.locator('.equals')).toContainText('equals: no');

    await weightTracker.weight_add( '11/01/2024', '213' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: no');
    await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
    await expect(page.locator('.less-than')).toContainText('less than: yes');
    await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
    await expect(page.locator('.equals')).toContainText('equals: no');

    await weightTracker.weight_add( '11/01/2024', '214' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');

    await weightTracker.weight_add( '11/02/2024', '250' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: no');
    await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
    await expect(page.locator('.less-than')).toContainText('less than: no');
    await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');
    await expect(page.locator('.equals')).toContainText('equals: yes');

    await weightTracker.weight_add( '11/03/2024', '280' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: no');

    await weightTracker.weight_add( '11/04/2024', '281' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: yes');

    await weightTracker.weight_add( '11/04/2024', '290' );

    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than')).toContainText('greater than: yes');
    await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: yes');
    await expect(page.locator('.less-than')).toContainText('less than: no');
    await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');
    await expect(page.locator('.equals')).toContainText('equals: no');

    await weightTracker.weight_add( '11/04/2024', '289' );
   
    await page.goto('http://localhost/if-statements/difference-from-start/');
    await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');

    await weightTracker.weight_set_defaults();

});
