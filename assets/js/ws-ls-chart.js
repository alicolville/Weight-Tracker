//
// To compress this script, use https://jscompress.com
//

jQuery( document ).ready( function ($ ) {

	// Search for any Weight Tracker charts and render chart.js
	$( '.ws-ls-chart' ).each( function () {

	  let chart_id = $( this ).attr('id' );

    ws_ls_render_graph( chart_id );

	});

	function ws_ls_render_graph( chart_id ) 	{

	  let chart_obj     = $( '#' + chart_id );
		let chart_type    = $( '#' + chart_id ).data('chart-type' );

		// If not specified, default to line
		if( 'undefined' === typeof chart_type ) {
			chart_type = 'line';
		}

		let ctx = chart_obj.get(0).getContext( '2d' );

		let width = chart_obj.parent().width();

    chart_obj.attr('width', width - 50);

    new Chart( ctx, { type: chart_type, data: this[ chart_id + "_data"], options: this[ chart_id + "_options" ] } );
	}
});
