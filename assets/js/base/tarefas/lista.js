$(document).ready(function(){

	function loadAll() {
		var form = $('#formFiltroTarefa');
		
		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'html',
			data: form.serializeArray(),
			success: function(json) {
				$('table tbody tr').remove();
				$('table tbody').append(json);
			},
			error: function() {
				alert('Erro na requisição AJAX!');
			}
		});
	}

	$('[name="sort_type"]').on("change", function(){
		var sort = $(this).find(":selected").attr("data-sort");
		$("#icon-sort").removeClass().addClass(sort);
	});

	$('[name="categoria"]').change(function(){
		var value = $(this).find(":selected").val();

		if(value == "") {
			$('.view-dev, .view-tester').hide();
			// should set field values to empty...
		}
		else if(value != 4) {
			$('.view-tester').hide();	
			$('.view-dev').show();
		}
		else {
			$('.view-tester').show();	
			$('.view-dev').hide();	
		}
	});

	$('[name="search_titulo"],[name="sort_type"]').on("change keyup", loadAll);

	$('body').on("click", ".btn-delete", function() {
		var id = $(this).parent().parent().attr("data-id");
		$('#formRemoveTarefaList [name="id"]').val(id);
		$('.help-edit').hide();
		$('.help-remove').hide();
		
		$.ajax({
			url: 'tarefas/get',
			method: 'POST',
			dataType: 'json',
			data: {id: id},
			success: function(json) {
				$('#formRemoveTarefaList #tarefa-titulo').text(json.titulo);
				$("#removeTarefaModalList").modal("show");
			},
			error: function() {
				alert('Erro na requisição AJAX!');
			}
		})
	});

	$('#formRemoveTarefaList').unbind("submit").bind("submit", function(e){
		e.preventDefault();
		var form = $(this);
		var id = $('#formRemoveTarefaList [name="id"]').val();

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$("#removeTarefaModalList").modal("hide");
					$('.task-removed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-removed-notification').fadeOut('fast');
					}, 2000);

					$('[data-tarefa-id="'+ id +'"]').parent().parent().parent().remove();
					$('.help-remove').show();					
					loadAll();
				}					
			},
			error: function() {
				alert('Erro na requisição AJAX!');
			}
		});
	});

	$('.material-switch input').click(function() {
		var text = $('#help-text-toggle').text();
		
		if(text == "Ativo:") {
			$('#help-text-toggle').fadeOut('fast', function(){
				$(this).text("Prontos:");
			}).fadeIn();
		}
		else {
			$('#help-text-toggle').fadeOut('fast', function(){
				$(this).text("Ativo:");
			}).fadeIn();
		}

		loadAll();
	});
});