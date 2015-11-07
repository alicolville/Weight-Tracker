<?php
	defined('ABSPATH') or die('Jog on!');

  function ws_ls_advanced_data_table($weight_data)
  {
      $html_output = '
      <table id="ws-ls-main-data-table" class="ws-ls-advanced-data-table display" width="95%">
        <thead>
        <tr>
          <th style="width:80px !important">' . __('Date', WE_LS_SLUG) .'</th>
          <th style="width:80px !important">' . __('Weight', WE_LS_SLUG) . '</th>
          <th style="width:60px">+/-</th>
          <th>' . __('Notes', WE_LS_SLUG) . '</th>
          <th>Order</th>
        </tr>
        </thead>
      <tbody>';

      foreach ($weight_data as $weight_object)
      {
                $html_output .= '<tr>
                            <td>' . ws_ls_render_date($weight_object) . '</td>
                            <td>' . $weight_object['display'] . '</td>
                            <td>' . (($weight_object['difference_from_start']) ? $weight_object['difference_from_start'] . '%' : '') . '</td>
                            <td style="font-size:0.7em;">' . $weight_object['notes'] . '</td>
                            <td>' . $weight_object['kg'] . '</td>
                          </tr>';
      }
      $html_output .= '</tbody>
      <tfoot>
        <tr>
          <th>' . __('Date', WE_LS_SLUG) .'</th>
          <th>' . __('Weight', WE_LS_SLUG) . '</th>
          <th>+/-</th>
          <th>' . __('Notes', WE_LS_SLUG) . '</th>
          <th></th>
        </tr>
      </tfoot>
      </table>';

      return $html_output;
  }
