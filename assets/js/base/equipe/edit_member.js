$(document).ready(function(){
	$('body').on("click", ".btn-edit", function () {
		// reset UI
		$('#formEditMember input[type="submit"]').prop("disabled", false);
		$('.help-create').hide();
		$('.error-create').hide();

		// update fields
		var id = $(this).parent().parent().data('id');
		$('#formEditMember [name="id"]').val(id);
		
		$.ajax({
			url: '../../equipe/get',
			method: 'POST',
			dataType: 'json',
			data: { id : id},
			success: function(json) {
				$('#formEditMember [name="nome"]').val(json.data.id_usuario);
				$('#formEditMember [name="funcao"]').val(json.data.funcao);
				$('#formEditMember [name="posicao"]').val(json.data.admin);
			}
		});

		$('#editMemberModal').modal("show");
	});

	$("#formEditMember").unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		if(validateForm('#formEditMember')) {
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

						$('#formEditMember input[type="submit"]').prop("disabled", true);
						setTimeout(function(){
							$('#formEditMember input[type="submit"]').prop("disabled", false);
						}, 500);

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