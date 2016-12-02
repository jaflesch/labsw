$(document).ready(function() {

	$('#insertCategoria').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$('.task-completed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-completed-notification').fadeOut('fast');
					}, 2000);

					$('[name="nome"]').val("");
				}
			}
		})
	});
});