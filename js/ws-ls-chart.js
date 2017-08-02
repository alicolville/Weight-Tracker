//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {

	if ("true" == ws_ls_config["is-pro"]) {

		// Render upon page
		$(".ws-ls-chart").each(function () {
		  $chart_id = $(this).attr("id");
		  ws_ls_render_graph($chart_id);
		});

		function ws_ls_render_graph($chart_id)
		{
			$chart_type = $("#" + $chart_id).data("chart-type");

			// If not specified, default to line
			if(typeof $chart_type === "undefined"){
				$chart_type = "line";
			}

			var ctx = $("#" + $chart_id).get(0).getContext("2d");

			var width = $("#" + $chart_id).parent().width();
			$("#" + $chart_id).attr("width",width-50);

			if ("line" == $chart_type) {
				new Chart(ctx, {type: "line", data: this[$chart_id + "_data"], options: this[$chart_id + "_options"]});
			}
			else if ("bar" == $chart_type) {

				new Chart(ctx, {type: "bar", data: this[$chart_id + "_data"], options: this[$chart_id + "_options"]});

			}
		}
	}
});
