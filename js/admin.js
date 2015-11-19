jQuery( document ).ready(function ($) {
  $("#ws-ls-tabs").zozoTabs({
      rounded: false,
       multiline: true,
       theme: "silver",
       size: "medium",
       responsive: true,
       animation: {
           effects: "slideH",
           easing: "easeInOutCirc",
           type: "jquery"
       }
  });
  // Disable all inputs for Pro rows
  $(".ws-ls-disabled input").prop('disabled', true);
  $(".ws-ls-disabled select").prop('disabled', true);

  $('.ws-ls-advanced-data-table').DataTable( {
          responsive: true,
          // / "order": [[ 3, "desc" ]]
          "lengthMenu": [[10, 25, 50, 100, 250, 500 -1], [10, 25, 50, 100, 250, 500, "All"]],
          "processing": true,
          "serverSide": true,
          "ajax": {
            "url" : ws_ls_user_data['ajax-url'],
            "data": {
                "action": "ws_ls_user_data"
            }
          },
          "order": [[ 1, "desc" ]],
          "columns": [
              { "name": "user_nicename" },
              { "name": "weight_date", "bSearchable": false  },
              { "name" : "weight_weight", "bSearchable": false },
              { "bSearchable": true, "bSortable": false },
              { "bSearchable": false, "bSortable": false }
          ]

      });

});
