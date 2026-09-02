import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

// Uses a date where day ≠ month so UK and US formats produce distinct strings:
//   UK  →  15/1/2024
//   US  →  1/15/2024
const TEST_DATE = '15/01/2024';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await wt.settings_set_date_format('false'); // ensure UK to start
        await wt.weight_add(TEST_DATE, '80');
        await use(wt);
        // Restore weight data and the site-wide date-format setting (not touched by weight_clear_all).
        await wt.weight_clear_all();
        await wt.settings_set_date_format('false');
    },
});

test.describe('[wt] date format switching', () => {

    test.describe.configure({ mode: 'serial' });

    test('history shows UK date format by default', async ({ wt, page }) => {
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('15/1/2024');
    });

    test('switching to US format changes date display', async ({ wt, page }) => {
        await wt.settings_set_date_format('true');
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('1/15/2024');
        await expect(page.locator('.ws-ls-user-data-ajax')).not.toContainText('15/1/2024');
    });

    test('switching back to UK format restores display', async ({ wt, page }) => {
        await wt.settings_set_date_format('true');
        await wt.settings_set_date_format('false');
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('15/1/2024');
    });
});
