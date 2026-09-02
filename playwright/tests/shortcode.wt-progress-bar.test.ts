import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

const PAGE = 'http://localhost/test-wt-progress-bar/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await wt.target_clear();
        await use(wt);
        await wt.weight_clear_all();
        await wt.target_clear();
    },
});

test.describe('[wt-progress-bar] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    // The shortcode checks for a target weight before it checks for a weight entry,
    // so with neither set it's the target error that shows, regardless of weight state.
    test('shows target error when no target is set', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.entry-content')).toContainText('enter a target weight');
        await expect(page.locator('.ws-ls-progress')).not.toBeVisible();
    });

    test('shows target error even when a weight entry exists', async ({ wt, page }) => {
        await wt.weight_add('01/01/2024', '80');
        await page.goto(PAGE);
        await expect(page.locator('.entry-content')).toContainText('enter a target weight');
        await expect(page.locator('.ws-ls-progress')).not.toBeVisible();
    });

    test('shows weight error when target exists but no weight entry', async ({ wt, page }) => {
        await wt.target_set('70');
        await page.goto(PAGE);
        await expect(page.locator('.entry-content')).toContainText('add a weight entry');
        await expect(page.locator('.ws-ls-progress')).not.toBeVisible();
    });

    test('renders progress bar when both weight and target exist', async ({ wt, page }) => {
        await wt.weight_add('01/01/2024', '80');
        await wt.target_set('70');
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-progress')).toBeVisible();
    });
});
