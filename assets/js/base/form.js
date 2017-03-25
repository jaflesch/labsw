$(document).ready(function() {
	$('#formContato').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				alert(json.msg);
				if(json.success) {
					$('#formContato input, #formContato textarea').val("");
				}
			}
		})

	});
});