import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_set_defaults();
        await use(wt);
    },
});

test.describe('[wt] unit switching', () => {

    test.describe.configure({ mode: 'serial' });

    test('history shows kg by default', async ({ wt, page }) => {
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
    });

    test('switching to pounds_only converts display', async ({ wt, page }) => {
        await wt.settings_set_unit('pounds_only');
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('440.92lbs');
        await expect(page.locator('.ws-ls-user-data-ajax')).not.toContainText('200kg');
    });

    test('switching back to kg restores display', async ({ wt, page }) => {
        await wt.settings_set_unit('pounds_only');
        await wt.settings_set_unit('kg');
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).not.toContainText('lbs');
    });
});
