import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const PAGE = 'http://localhost/test-wt-table/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_set_defaults();
        await use(wt);
    },
});

test.describe('[wt-table] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('renders entry row', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('1/1/2019');
    });

    test('edit entry updates displayed weight', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');

        // Click edit on the first row – FooTable navigates to ?ws-edit-entry=<id>
        await page.locator('.footable-edit').first().click();
        await expect(page.getByTestId('ws-form-weight')).toBeVisible();

        await page.getByTestId('ws-form-weight').fill('150');
        await page.locator('.ws-ls-save-entry-button-weight').click();

        // After redirect back, table should reflect the updated weight
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('150kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).not.toContainText('200kg');
    });

    test('delete entry removes row', async ({ wt, page }) => {
        await wt.weight_add('11/02/2019', '180');
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('180kg');

        page.on('dialog', dialog => dialog.accept());
        await page.locator('.footable-delete').first().click();

        // FooTable removes the row optimistically; one weight should disappear
        const rows = page.locator('.ws-ls-user-data-ajax tbody tr:not(.footable-detail-row)');
        await expect(rows).toHaveCount(1);
    });
});
