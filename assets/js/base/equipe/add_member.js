$(document).ready(function(){
	$('body').on("click", "#btn-add-member", function () {
		// reset fields
		$('#formAddMember [name="nome"]').val("");
		$('#formAddMember [name="funcao"]').val("");
		$('#formAddMember [name="posicao"]').val("");

		// reset UI
		$('#formAddMember input[type="submit"]').prop("disabled", false);
		$('.help-create').hide();
		$('.error-create').hide();
	});

	$("#formAddMember").unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		if(validateForm('#formAddMember')) {
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

						$('#formAddMember input[type="submit"]').prop("disabled", true);

						// update
						loadAll();
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