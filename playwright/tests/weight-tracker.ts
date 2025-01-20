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
}