import type { Page, Locator } from '@playwright/test';
import { expect } from '@playwright/test';
/**
 * This class is used  by other tests to simulate Weight Tracker [wt] actions
 */

export class WeightTracker {

  constructor(public readonly page: Page) {
  }

  async goto() {
      await this.page.goto('http://localhost/');
  }

  async weight_set_defaults(){
      await this.goto();
      await this.weight_clear_all();
      await this.weight_add( '01/01/2019', '200' );
  }

  async weight_add( date, weight){
      await this.goto();
      await this.page.getByTestId('wt-tab-add-edit').click();
      await this.page.getByTestId('we-ls-date').fill(date);
      await this.page.keyboard.press('Escape'); // Close the jQuery UI datepicker, which otherwise overlaps the tab link below
      await this.page.getByTestId('wt-tab-add-edit').click();
      await this.page.getByTestId('ws-form-weight').fill(weight);
      await this.page.locator('.ws-ls-save-entry-button-weight').click();

  }

  async weight_clear_all(){
      await this.goto();
      await this.page.getByTestId('wt-tab-settings').click();
      await this.page.getByLabel('The button below allows you').selectOption('yes');
      await this.page.getByRole('button', { name: 'Delete' }).click();
      await expect(this.page.locator('#wp--skip-link--target')).toContainText('Your data has successfully been deleted.');
  }

  async target_set(target){
      await this.goto();
      await this.page.getByRole('link', { name: 'Adjust' }).click();
      await this.page.getByTestId('ws-form-target').click();
      await this.page.getByTestId('ws-form-target').fill( target);
      await this.page.locator('.ws-ls-save-entry-button-target').click();
  }

  async target_clear(){
    await this.goto();
    await this.page.getByTestId('wt-tab-settings').click();
      
    const visible = await this.page.getByRole('button', { name: 'Clear Target' }).isVisible();
    
    if (visible) {
      this.page.on('dialog', dialog => dialog.accept());
      await this.page.getByRole('button', { name: 'Clear Target' }).click();
    }    
  }

  async settings_set_unit(unit: string) {
      await this.goto();
      await this.page.getByTestId('wt-tab-settings').click();
      await this.page.getByLabel('In which unit would you like').selectOption(unit);
      await this.page.getByRole('button', { name: 'Save Settings' }).click();
  }

  async settings_set_date_format(format: string) {
      await this.goto();
      await this.page.getByTestId('wt-tab-settings').click();
      await this.page.getByLabel('Display dates in the').selectOption(format);
      await this.page.getByRole('button', { name: 'Save Settings' }).click();
  }

  async settings_set_height(height: string) {
      await this.goto();
      await this.page.getByTestId('wt-tab-settings').click();
      await this.page.getByLabel('Your height:').selectOption(height);
      await this.page.getByRole('button', { name: 'Save Settings' }).click();
  }

  // Returns today's date as DD/MM/YYYY (UK input format)
  get_today_uk(): string {
      const d = new Date();
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      return `${dd}/${mm}/${d.getFullYear()}`;
  }

  async history_wait_for_loaded() {
      await this.goto();
      await this.page.getByTestId('wt-tab-history').click();
      // Wait until FooTable removes the loading class after its AJAX call
      await this.page.locator('.ws-ls-user-data-ajax:not(.ws-ls-loading-table)').waitFor();
  }
}