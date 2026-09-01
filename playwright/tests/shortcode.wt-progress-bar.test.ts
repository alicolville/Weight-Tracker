import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const PAGE = 'http://localhost/test-wt-progress-bar/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await wt.target_clear();
        await use(wt);
    },
});

test.describe('[wt-progress-bar] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('shows error when no weight entry', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-blockquote')).toContainText('add a weight entry');
        await expect(page.locator('.ws-ls-progress')).not.toBeVisible();
    });

    test('shows error when weight exists but no target', async ({ wt, page }) => {
        await wt.weight_add('01/01/2024', '80');
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-blockquote')).toContainText('target weight');
        await expect(page.locator('.ws-ls-progress')).not.toBeVisible();
    });

    test('renders progress bar when both weight and target exist', async ({ wt, page }) => {
        await wt.weight_add('01/01/2024', '80');
        await wt.target_set('70');
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-progress')).toBeVisible();
        await expect(page.locator('.ws-ls-blockquote')).not.toBeVisible();
    });
});
