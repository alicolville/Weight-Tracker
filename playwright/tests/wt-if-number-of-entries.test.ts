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
  
        await page.goto('http://localhost/if-statements/number-of-entries/');
        await expect(page.locator('.greater-than')).toContainText('greater than: no');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
        await expect(page.locator('.less-than')).toContainText('less than: yes');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: yes');

        await weightTracker.weight_add( '01/01/2024', '300' );

        await page.goto('http://localhost/if-statements/number-of-entries/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: no');
        await expect(page.locator('.less-than')).toContainText('less than: yes');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '01/02/2024', '400' ); // 3 entries

        await page.goto('http://localhost/if-statements/number-of-entries/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: yes');
        await expect(page.locator('.less-than')).toContainText('less than: no');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '01/03/2024', '500' ); // 4 entries

        await page.goto('http://localhost/if-statements/number-of-entries/');
        await expect(page.locator('.greater-than')).toContainText('greater than: yes');
        await expect(page.locator('.greater-than-or-equal-to')).toContainText('greater than or equal to: yes');
        await expect(page.locator('.less-than')).toContainText('less than: no');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: yes');
        await expect(page.locator('.equals')).toContainText('equals: no');

        await weightTracker.weight_add( '01/04/2024', '600' ); // 4 entries

        await page.goto('http://localhost/if-statements/number-of-entries/');
        await expect(page.locator('.less-than-or-equal-to')).toContainText('less than or equal to: no');

        await weightTracker.weight_set_defaults();

    });
