import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const PAGE = 'http://localhost/test-wt-form/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await use(wt);
    },
});

test.describe('[wt-form] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('submitting weight entry shows success message', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.getByTestId('ws-form-weight')).toBeVisible();

        await page.getByTestId('we-ls-date').fill('01/06/2024');
        await page.keyboard.press('Escape');
        await page.getByTestId('ws-form-weight').fill('85');
        await page.locator('.ws-ls-save-entry-button-weight').click();

        await expect(page.locator('.ws-ls-blockquote')).toContainText('successfully saved');
    });

    test('saved entry appears in main shortcode history', async ({ wt, page }) => {
        await page.goto(PAGE);
        await page.getByTestId('we-ls-date').fill('01/06/2024');
        await page.keyboard.press('Escape');
        await page.getByTestId('ws-form-weight').fill('85');
        await page.locator('.ws-ls-save-entry-button-weight').click();

        await wt.history_wait_for_loaded();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('85kg');
    });
});
