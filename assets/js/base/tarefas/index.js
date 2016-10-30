$(document).ready(function(){

	// display mode
	$('.display-filter span.glyphicon').click(function() {
		var target = $(this).data("target");

		// switch display
		$('#view-list,#view-table').hide();
		$(target).show();

		// switch active icon
		$('.display-filter span.selected').removeClass('selected');
		$(this).addClass('selected');
	});
});