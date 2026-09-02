import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

// Test page contains: [wt-reminder type="target"]
const PAGE = 'http://localhost/test-wt-reminder/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.target_clear();
        await use(wt);
        await wt.target_clear();
    },
});

test.describe('[wt-reminder] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('shows reminder when no target is set', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-reminder')).toBeVisible();
        await expect(page.locator('.ws-ls-reminder')).toContainText('target weight');
    });

    test('hides reminder once target is set', async ({ wt, page }) => {
        await wt.target_set('70');
        await page.goto(PAGE);
        await expect(page.locator('.ws-ls-reminder')).not.toBeVisible();
    });
});
