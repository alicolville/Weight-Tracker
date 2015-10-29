jQuery( window ).ready(function ($) {

  if ('true' == ws_ls_config['tabs-enabled']) {
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
  }

  // Render upon page load
  $('.ws-ls-chart').each(function () {
    $chart_id = $(this).attr('id');
    ws_ls_render_graph($chart_id);
  });

  function ws_ls_render_graph($chart_id)
  {
      $chart_type = $("#" + $chart_id).data('chart-type');

      // If not specified, default to line
      if(typeof $chart_type === 'undefined'){
          $chart_type = 'line';
      }

      var ctx = $("#" + $chart_id).get(0).getContext("2d");

      var width = $("#" + $chart_id).parent().width();
      $("#" + $chart_id).attr("width",width-50);

      if ('line' == $chart_type) {
          new Chart(ctx).Line(this[$chart_id + "_data"], this[$chart_id + "_options"]);
      }
      else if ('bar' == $chart_type) {

        $target_colour = $("#" + $chart_id).data('target-colour');
        $target_weight = $("#" + $chart_id).data('target-weight');

        // Based on http://stackoverflow.com/questions/28076525/overlay-line-on-chart-js-graph
        Chart.types.Bar.extend({
              name: 'BarOverlay',
              draw: function (ease) {
                  Chart.types.Bar.prototype.draw.apply(this);
                  ctx.beginPath();
                  ctx.lineWidth = 2;
                  ctx.strokeStyle = $target_colour;
                  ctx.moveTo(35, this.scale.calculateY($target_weight));
                  ctx.lineTo(this.scale.calculateX(this.datasets[0].bars.length), this.scale.calculateY($target_weight));
                  ctx.stroke();
              }
          });

          new Chart(ctx).BarOverlay(this[$chart_id + "_data"], this[$chart_id + "_options"]);
      }

  }

  $('.we-ls-datepicker').each(function() {
    var options = {
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: ws_ls_config['date-format']
                };

    $(this).datepicker(options);
  });

  // Form Validation
  $('.we-ls-weight-form-validate').each(function () {

    $form_id = $(this).attr('id');
    $target_form = $(this).data('is-target-form');
    $weight_unit = $(this).data('metric-unit');

    console.log('Adding form validation to: ' + $form_id + '. Target form? ' + $target_form + '. Weight Unit: ' + $weight_unit);

    // Add form validation
    $( "#" + $form_id ).validate({
      errorClass: "ws-ls-invalid",
      validClass: "ws-ls-valid",
      //focusCleanup: true,
      errorContainer: "#" + $form_id + " .ws-ls-error-summary",
      errorLabelContainer: "#" + $form_id + " .ws-ls-error-summary ul",
      wrapper: "li",
      messages: {
          'we-ls-date': ws_ls_config['validation-we-ls-date'],
          'we-ls-weight-pounds': ws_ls_config['validation-we-ls-weight-pounds'],
          'we-ls-weight-kg': ws_ls_config['validation-we-ls-weight-kg'],
          'we-ls-weight-stones': ws_ls_config['validation-we-ls-weight-stones']
      },
      submitHandler: function(form) {
          form.submit();
      }
    });

    // Non Target form specific fields
    if (!$target_form) {
      // Validate date
      if ('true' == ws_ls_config['us-date']) {
        $( "#" + $form_id + " #we-ls-date" ).rules( "add", {
          required: true,
          date: true
        });
      }
      else {
        $( "#" + $form_id + " #we-ls-date" ).rules( "add", {
          required: true,
          dateITA: true
        });
      }

    }
    // Set up numeric fields to validate
    if('imperial-pounds' == $weight_unit)
    {
        $( "#" + $form_id + " #we-ls-weight-pounds").rules( "add", {
          required: true,
          number: true,
          range: [0, 5000]
        });
    }
    if('imperial-both' == $weight_unit)
    {
        $( "#" + $form_id + " #we-ls-weight-stones").rules( "add", {
          required: true,
          number: true,
          range: [0, 5000] // Stupid high in case not tracking human weight!
        });
        $( "#" + $form_id + " #we-ls-weight-pounds").rules( "add", {
          required: true,
          number: true,
          range: [0, 14]
        });
    }
    if('metric' == $weight_unit)
    {
        $( "#" + $form_id + " #we-ls-weight-kg").rules( "add", {
          required: true,
          number: true,
          range: [0, 50000] // Stupid high in case not tracking human weight!
        });
    }
  });


});
