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

});
