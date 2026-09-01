import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const PAGE = 'http://localhost/test-wt-chart/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_set_defaults();
        await use(wt);
    },
});

test.describe('[wt-chart] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('renders canvas when data exists', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('canvas.ws-ls-chart')).toBeVisible();
        await expect(page.locator('.ws-ls-chart-container')).toBeVisible();
    });

    test('shows no-data message when no entries', async ({ wt, page }) => {
        await wt.weight_clear_all();
        await page.goto(PAGE);
        await expect(page.locator('canvas.ws-ls-chart')).not.toBeVisible();
        await expect(page.locator('.ws-ls-blockquote')).toContainText('no data');
    });
});
