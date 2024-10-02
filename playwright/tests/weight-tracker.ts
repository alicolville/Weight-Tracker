import type { Page, Locator } from '@playwright/test';
import { expect } from '@playwright/test';
/**
 * This class is used  by other tests to simulate Weight Tracker [wt] actions
 */

export class WeightTracker {
//   private readonly inputBox: Locator;
//   private readonly todoItems: Locator;

  constructor(public readonly page: Page) {
    // this.inputBox = this.page.locator('input.new-todo');
    // this.todoItems = this.page.getByTestId('todo-item');
  }

async goto() {
    await this.page.goto('http://localhost/weight-tracker/');
}

async weight_set_defaults(){
   // this.goto();
    await this.weight_clear_all();
    await this.weight_add( '01/01/2019', '200' );
}

async weight_add( date, weight){
  //  this.goto();
    await this.page.getByTestId('wt-tab-add-edit').click();
    await this.page.getByTestId('we-ls-date').fill(date);
    await this.page.getByTestId('wt-tab-add-edit').click();
    await this.page.getByTestId('ws-form-weight').fill(weight);
    await this.page.getByRole('button', { name: 'Save Entry' }).click();
}

async weight_clear_all(){
  //  this.goto();
    await this.page.getByTestId('wt-tab-settings').click();
    await this.page.getByLabel('The button below allows you').selectOption('yes');
    await this.page.getByRole('button', { name: 'Delete' }).click();
    await expect(this.page.locator('#wp--skip-link--target')).toContainText('Your data has successfully been deleted.');
}

   
}