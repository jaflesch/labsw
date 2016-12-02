$(document).ready(function() {

	$('.btn-delete').click(function() {
		var id = $(this).data('id');
		$('#formRemoveCategoria [name="id"]').val(id);

		$.ajax({
			url: 'categorias/get',
			method: 'POST',
			dataType: 'json',
			data: { id : id},
			success: function(json) {
				$('#formRemoveCategoria #categoria-nome').text(json.nome);
				$('#removeCategoriaModal').modal('show');
			}
		});
	});

	$('#formRemoveCategoria').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var id = $('#formRemoveCategoria [name="id"]').val();
		var form = $(this);

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: { id: id },
			success: function(json) {
				if(json.success) {
					$('[data-id="'+ id +'"]').parent().parent().remove();
					$('#removeCategoriaModal').modal('hide');
					
					$('.task-removed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-removed-notification').fadeOut('fast');
					}, 2000);
				}
			}
		})
	});

	$('#editCategoria').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$(button).parent().parent().remove();

					$('.task-completed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-completed-notification').fadeOut('fast');
					}, 2000);
				}
			}
		})
	});
});