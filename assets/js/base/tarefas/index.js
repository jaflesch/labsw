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
		$('#formRemoveTarefaTable [name="src"]').val('table');
		$('#formRemoveTarefaTable [name="id"]').val(tarefa_id);

		$.ajax({
			url: 'tarefas/get',
			method: 'POST',
			dataType: 'json',
			data: {id: tarefa_id},
			success: function(json) {
				$('#formRemoveTarefaTable #tarefa-titulo').text(json.titulo);
				$("#removeTarefaModalTable").modal("show");
			},
			error: function() {
				alert('Erro na requisição AJAX!');
			}
		});
	});

	$('#view-list .btn-delete').click(function() {
		var tarefa_id = $(this).parent().parent().data('id');

		$('#formRemoveTarefaList [name="id"]').val(tarefa_id);
		$('#removeTarefaModalList').modal('show');
	})

	$('#formRemoveTarefaTable').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);
		var id = $('#formRemoveTarefaTable [name="id"]').val();

		$.ajax({
			url: form.attr('action'),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$("#removeTarefaModalTable").modal("hide");
					$('.task-removed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-removed-notification').fadeOut('fast');
					}, 2000);

					$('[data-tarefa-id="'+ id +'"]').parent().parent().parent().remove();
					$('#view-list [data-id="' + id +'"]').remove();
				}
				else alert("Erro ao executar script! Tente novamente.");
			}
		});
	})


});