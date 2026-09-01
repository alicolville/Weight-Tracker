import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

// Test page contains:
//   <div class="wt-test-msg-lost">[wt-message type="lost"]Losing weight![/wt-message]</div>
//   <div class="wt-test-msg-gained">[wt-message type="gained"]Gaining weight![/wt-message]</div>
const PAGE = 'http://localhost/test-wt-message/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await use(wt);
    },
});

test.describe('[wt-message] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('no message shown when insufficient data', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.wt-test-msg-lost')).toBeEmpty();
        await expect(page.locator('.wt-test-msg-gained')).toBeEmpty();
    });

    test('loss message shown after consecutive weight loss', async ({ wt, page }) => {
        // Two entries with decreasing weight → "lost" condition met
        await wt.weight_add('01/01/2024', '100');
        await wt.weight_add('02/01/2024', '98');
        await page.goto(PAGE);
        await expect(page.locator('.wt-test-msg-lost')).toContainText('Losing weight!');
        await expect(page.locator('.wt-test-msg-gained')).toBeEmpty();
    });

    test('gain message shown after consecutive weight gain', async ({ wt, page }) => {
        // Two entries with increasing weight → "gained" condition met
        await wt.weight_add('01/01/2024', '90');
        await wt.weight_add('02/01/2024', '92');
        await page.goto(PAGE);
        await expect(page.locator('.wt-test-msg-gained')).toContainText('Gaining weight!');
        await expect(page.locator('.wt-test-msg-lost')).toBeEmpty();
    });
});
