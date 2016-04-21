<?php
	defined('ABSPATH') or die('Jog on!');

  function ws_ls_advanced_data_table($weight_data)
  {
			$table_id = 'ws_ls_data_table_' . rand(10,1000) . '_' . rand(10,1000);

      $html_output = '
      <table id="' . $table_id . '" class="display ws-ls-advanced-data-table" cellspacing="0" width="100%">
        <thead>
        <tr>
          <th>' . __('Date', WE_LS_SLUG) .'</th>
          <th>' . __('Weight', WE_LS_SLUG) . '</th>
          <th>+/-</th>
          <th class="tablet-l">' . __('Notes', WE_LS_SLUG) . '</th>
          <th class="never"></th>
					<th>Options</th>
        </tr>
        </thead>
      <tbody>';

			$delete_image = plugins_url( '../css/images/delete.png', __FILE__ );
			$edit_image = plugins_url( '../css/images/edit.png', __FILE__ );

      foreach ($weight_data as $weight_object)
      {
                $html_output .= '<tr id="ws-ls-row-' . $weight_object['db_row_id'] . '">
                            <td>' . ws_ls_render_date($weight_object) . '</td>
                            <td>' . $weight_object['display'] . '</td>
                            <td>' . ((isset($weight_object['difference_from_start']) && is_numeric($weight_object['difference_from_start'])) ? $weight_object['difference_from_start'] . '%' : '') . '</td>
                            <td style="font-size:0.7em;word-wrap:break-word;">' . $weight_object['notes'] . '</td>
                            <td>' . $weight_object['kg'] . '</td>
														<td class="ws-ls-table-options">

																<img src="' . $edit_image .'" width="15" height="15" id="ws-ls-edit-' . $weight_object['db_row_id'] . '" data-row-id="' . $weight_object['db_row_id'] . '" class="ws-ls-edit-row" />
																<img src="' . $delete_image .'" width="15" height="15" id="ws-ls-delete-' . $weight_object['db_row_id'] . '" data-row-id="' . $weight_object['db_row_id'] . '" class="ws-ls-delete-row" />

														</td>
                          </tr>';
      }
      $html_output .= '</tbody>

      </table>';

      return $html_output;
  }

	function ws_ls_advanced_table_locale() {

		return array(
				"decimal"=>         "",
				"emptyTable"=>      __('No data available in table', WE_LS_SLUG),
				"info"=>            __("Showing _START_ to _END_ of _TOTAL_ entries", WE_LS_SLUG),
				"infoEmpty"=>       __("Showing 0 to 0 of 0 entries", WE_LS_SLUG),
				"infoFiltered"=>    __("(filtered from _MAX_ total entries)", WE_LS_SLUG),
				"infoPostFix"=>     "",
				"thousands"=>       ",",
				"lengthMenu"=>      __("Show _MENU_ entries", WE_LS_SLUG),
				"loadingRecords"=>  __("Loading...", WE_LS_SLUG),
				"processing"=>      __("Processing...", WE_LS_SLUG),
				"search"=>          __("Search=> ", WE_LS_SLUG),
				"zeroRecords"=>     __("No matching records found", WE_LS_SLUG),
				"paginate"=>  array(
				    "first"=>       __("First", WE_LS_SLUG),
				    "last"=>        __("Last", WE_LS_SLUG),
				    "next"=>        __("Next", WE_LS_SLUG),
				    "previous"=>    __("Previous", WE_LS_SLUG)
				),
				"aria"=>  array(
				    "sortAscending"=>   __(":  activate to sort column ascending", WE_LS_SLUG),
				    "sortDescending"=>  __(": activate to sort column descending", WE_LS_SLUG)
				)
		);
	}
