import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_set_defaults();
        await use(wt);
    },
});

test.describe('[wt] history tab', () => {

    test.describe.configure({ mode: 'serial' });

    test('shows added entry in table', async ({ wt, page }) => {
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('1/1/2019');
    });

    test('shows multiple entries after adding a second', async ({ wt, page }) => {
        await wt.weight_add('15/03/2020', '185');
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('185kg');
    });

    test('notes column shows entry note', async ({ wt, page }) => {
        await wt.weight_clear_all();
        await page.goto('http://localhost/');
        await page.getByTestId('wt-tab-add-edit').click();
        await page.getByTestId('we-ls-date').fill('01/01/2019');
        await page.keyboard.press('Escape');
        await page.getByTestId('wt-tab-add-edit').click();
        await page.getByTestId('ws-form-weight').fill('200');
        await page.getByTestId('we-ls-notes').fill('My test note');
        await page.locator('.ws-ls-save-entry-button-weight').click();
        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('My test note');
    });
});
