$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip({
		  placement : 'top'
	});

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

	$('#view-table .btn-close').click(function() {
		var tarefa_id = $(this).data('tarefa-id');

		$('#formRemoveTarefa [name="id"]').val(tarefa_id);
		$('#removeTarefaModal').modal('show');
	});

	$('#view-list .btn-delete').click(function() {
		var tarefa_id = $(this).parent().parent().data('id');

		$('#formRemoveTarefa [name="id"]').val(tarefa_id);
		$('#removeTarefaModal').modal('show');
	})

	$('#formRemoveTarefa').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);
		var id = $('#formRemoveTarefa [name="id"]').val();

		$.ajax({
			url: form.attr('action'),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$("#removeTarefaModal").modal("hide");
					$('.task-removed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-removed-notification').fadeOut('fast');
					}, 2000);

					$('[data-tarefa-id="'+ id +'"]').parent().parent().parent().remove();
					$('[data-id="' + id +'"]').parent().parent().remove();
				}
				else alert("Erro ao executar script! Tente novamente.");
			}
		});
	})


});