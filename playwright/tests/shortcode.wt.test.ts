import { test, expect } from '@playwright/test';

test.describe( 'WT Shortcode', () => {

    test.describe.configure( { mode: 'serial' } );
    
  
    test('exist on page', async ({ page }) => {
        await page.goto('http://localhost/weight-tracker/');
       
        await expect(page).toHaveTitle(/Weight Tracker/);
      
        // If the following is true, can assume shortcode is rendering when logged in
        await expect(page.getByRole('link', { name: 'VIEW IN TABULAR FORMAT' })).toBeVisible();
    });
    
    test('set user settings', async ({ page }) => {

        await page.goto('http://localhost/weight-tracker/');
    
        await page.getByTestId('wt-tab-settings').click();
       
        await expect(page.getByLabel('Your aim:')).toBeVisible();

        await page.getByTestId('wt-tab-settings').click();
        await page.getByLabel('Your aim:').selectOption('3');
        await page.getByLabel('Your height:').selectOption('188');
        await page.getByLabel('Your Gender:').selectOption('2');
        await page.getByLabel('Your Activity Level:').selectOption('1.55');
        await page.getByLabel('Your Date of Birth:').click();
        await page.getByRole('link', { name: '7', exact: true }).click();
        await page.getByLabel('Your Date of Birth:').click();
        await page.getByLabel('Select year').selectOption('1992');
        await page.getByLabel('Select month').selectOption('5');
        await page.getByRole('link', { name: '19' }).click();
        await page.getByRole('button', { name: 'Save Settings' }).click();
    
        await expect(page.getByText('Your settings have been')).toBeVisible();

        await page.getByTestId('wt-tab-settings').click();

        await expect(page.getByLabel('Your aim:')).toBeVisible();
        await expect(page.getByLabel('Your aim:')).toContainText('Gain weight');
        await expect(page.getByLabel('Your height:')).toContainText('188cm – 6\' 2');
        await expect(page.getByLabel('Your Gender:')).toContainText('Male');
        await expect(page.getByLabel('Your Activity Level:')).toContainText('Moderate Exercise (3-5 days a week)');
        await expect(page.getByLabel('Challenges')).toContainText('No – Do not opt me into any challenges');
        await expect(page.getByLabel('In which unit would you like')).toContainText('Kg');
        await expect(page.getByLabel('Display dates in the')).toContainText('UK (DD/MM/YYYY)');
        await expect(page.getByLabel('Your Date of Birth:')).toHaveValue('19/06/1992');
    });

    test('add a weight', async ({ page }) => {

        await page.goto('http://localhost/weight-tracker/');
    
        // Start by deleting all weight entries
        await page.getByTestId('wt-tab-settings').click();
        await page.getByLabel('The button below allows you').selectOption('yes');
        await page.getByRole('button', { name: 'Delete' }).click();
        await expect(page.locator('#wp--skip-link--target')).toContainText('Your data has successfully been deleted.');

        // Add a new weight entry
        await page.getByTestId('wt-tab-add-edit').click();

        await page.getByTestId('we-ls-date').click();
        await page.getByLabel('Select month').selectOption('0');
        await page.getByLabel('Select year').selectOption('2019');
        await page.getByRole('link', { name: '1', exact: true }).click();
        await expect(page.getByTestId('we-ls-date')).toHaveValue('01/01/2019');
        await page.getByTestId('ws-form-weight').fill('200');
        await page.getByTestId('we-ls-notes').fill('Add a wee note here');
        await page.getByRole('button', { name: 'Save Entry' }).click();

        // Validate that it was added
        await page.getByTestId('wt-tab-history').click();
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('200kg');
        await expect(page.locator('.ws-ls-user-data-ajax')).toContainText('1/1/2019');
    });

    test('add a target', async ({ page }) => {

        await page.goto('http://localhost/weight-tracker/');
        await page.getByTestId('wt-tab-settings').click();
        await page.getByTestId('ws-form-target').click();
        await page.getByTestId('ws-form-target').fill('455');
        await page.getByRole('button', { name: 'Set Target' }).click();
        await page.getByTestId('wt-tab-settings').click();
    
        await expect(page.getByText('Your target weight is 455kg.')).toBeVisible();

        page.on('dialog', dialog => dialog.accept());

        await page.getByRole('button', { name: 'Clear Target' }).click();

        await page.getByTestId('wt-tab-settings').click();
        await page.getByTestId('ws-form-target').click();
        await expect(page.getByTestId('ws-form-target')).toBeEmpty();
    });
});


function delay(ms: number) {
    return new Promise( resolve => setTimeout(resolve, ms) );
}    