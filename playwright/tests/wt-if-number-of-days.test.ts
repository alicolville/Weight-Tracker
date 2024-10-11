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

    test('basic tests', async ({ weightTracker, page }) => {
  
        await page.goto('http://localhost/if-statements/number-of-days/');
        await expect(page.locator('.greater-than')).toContainText('greater than: no');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
        await expect(page.locator('.less-than')).toContainText('less than: yes');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '11/01/2019', '300' );

        await page.goto('http://localhost/if-statements/number-of-days/');
        await expect(page.locator('.greater-than')).toContainText('greater than: no');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
        await expect(page.locator('.less-than')).toContainText('less than: yes');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: yes');

        await weightTracker.weight_add( '16/01/2019', '200' );

        await page.goto('http://localhost/if-statements/number-of-days/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: yes');
        await expect(page.locator('.less-than')).toContainText('less than: yes');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '26/01/2019', '200' );

        await page.goto('http://localhost/if-statements/number-of-days/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: yes');
        await expect(page.locator('.less-than')).toContainText('less than: no');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '26/02/2019', '200' );

        await page.goto('http://localhost/if-statements/number-of-days/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');

        await weightTracker.weight_set_defaults();

    });
