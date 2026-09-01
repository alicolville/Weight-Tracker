import { test as base, expect } from '@playwright/test';
import { WeightTracker } from './weight-tracker';

// Test page contains:
//   <span class="wt-test-bmi-index">[wt-bmi display="index"]</span>
//   <span class="wt-test-bmi-label">[wt-bmi display="label"]</span>
const PAGE = 'http://localhost/test-wt-bmi/';

const test = base.extend<{ wt: WeightTracker }>({
    wt: async ({ page }, use) => {
        const wt = new WeightTracker(page);
        await wt.weight_clear_all();
        await use(wt);
    },
});

test.describe('[wt-bmi] shortcode', () => {

    test.describe.configure({ mode: 'serial' });

    test('shows weight-needed placeholder when no entries', async ({ wt, page }) => {
        await page.goto(PAGE);
        await expect(page.locator('.wt-test-bmi-index')).toContainText('Weight needed');
    });

    test('shows height-needed placeholder when no height set', async ({ wt, page }) => {
        // Add weight but leave height unset (cleared by weight_clear_all)
        await wt.weight_add('01/01/2024', '80');
        await page.goto(PAGE);
        await expect(page.locator('.wt-test-bmi-index')).toContainText('Height needed');
    });

    test('shows numeric BMI and label when height and weight both set', async ({ wt, page }) => {
        // 80 kg at 188 cm → BMI ≈ 22.6 → Healthy
        await wt.weight_add('01/01/2024', '80');
        await wt.settings_set_height('188');
        await page.goto(PAGE);
        // Index should be a number (not a placeholder string)
        const bmiText = await page.locator('.wt-test-bmi-index').textContent();
        expect(parseFloat(bmiText ?? '')).toBeGreaterThan(0);
        // Label should reflect a BMI category
        await expect(page.locator('.wt-test-bmi-label')).not.toContainText('needed');
    });
});
