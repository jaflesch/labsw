$(document).ready(function(){
	// update subcategories
	$('[name="categoria"]').change(function() {
		$.ajax({
			url: '../tarefas/get-subcategoria',
			method: 'POST',
			dataType: 'html',
			data: { id: $(this).val()},
			success: function(json) {
				$('[name="subcategoria"] option').remove();
				$('[name="subcategoria"]').append(json);
			}
		});
	});

	// show/hide task target field
	$('[name="responsavel_tarefa"]').change(function() {
		var val = $(this).val();

		if(val == '-1') {
			$('.member-resp-field').show();
		}
		else {
			$('.member-resp-field').hide();	
		}
	});

	// update team list
	$('[name="projeto"]').change(function() {
		$.ajax({
			url: '../tarefas/get-team',
			method: 'POST',
			dataType: 'html',
			data: { id: $(this).val()},
			success: function(json) {
				$('[name="responsavel_membro_tarefa"] option').remove();
				$('[name="responsavel_membro_tarefa"]').append(json);
			}
		});
	})

	$('[name="subcategoria"]').change(function() {
		var id_categoria = $('[name="categoria"]').val();
		var id_subcategoria = $(this).val();
		var nome = $('[name="subcategoria"]').find(":selected").text();
		
		$.ajax({
			url: '../tarefas/get-average-time',
			method: 'POST',
			dataType: 'json',
			data: {
				id_categoria: id_categoria,
				id_subcategoria: id_subcategoria,
				nome: nome
			},
			success: function(json) {
				if(json.success) {
					$('#average-time-info span').html(json.msg);
					$('#average-time-info').fadeIn();	
				}
				else {
					$('#average-time-info span').html("");
					$('#average-time-info').hide();
				}
			}
		});
	})
});