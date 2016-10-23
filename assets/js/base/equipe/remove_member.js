$(document).ready(function(){
	$('body').on("click", ".btn-delete", function () {
		var nome = $($(this).parent().siblings()[0]).text();
		$('#member-name').text(nome);
		
		var id = $(this).parent().parent().data('id');
		$('#formRemoveMember [name="id"]').val(id);
		
		$('#removeMemberModal').modal("show");
	});

	$("#formRemoveMember").unbind("submit").bind("submit", function(e) {
		e.preventDefault();

		var form = $(this);

		if(validateForm('#formRemoveMember')) {
			$.ajax({
				url: form.attr("action"),
				method: 'POST',
				dataType: 'json',
				data: form.serializeArray(),
				success: function(json) {
					if(json.success) {
						$("#removeMemberModal").modal('hide');
						
						$('.task-removed-notification').fadeIn('fast');
						setTimeout(function(){
							$('.task-removed-notification').fadeOut('fast');
						}, 2000);

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