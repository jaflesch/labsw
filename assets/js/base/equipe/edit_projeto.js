$(document).ready(function(){
	$("#editProjeto").unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		if(validateForm('#editProjeto')) {
			$.ajax({
				url: form.attr("action"),
				method: 'POST',
				dataType: 'json',
				data: form.serializeArray(),
				success: function(json) {
					if(json.success) {
						$('.error-create').hide();
						$('.help-create').show();
						$('.help-create #json-msg').text(json.msg);

						$('#editProjeto button').prop("disabled", true);
						setTimeout(function(){
							$('#editProjeto button').prop("disabled", false);
						}, 750);

						$('.task-completed-notification').fadeIn('fast');
						setTimeout(function(){
							$('.task-completed-notification').fadeOut('fast');
						}, 2000);
					}
					else {
						$('.help-create').hide();
						$('.error-create').show();
						$('.error-create #json-msg').text(json.msg);
					}
				}
			});
		}
		else alert('Por favor, preencha os campos obrigatórios!');
	});
});