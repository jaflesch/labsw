$(document).ready(function(){
	// WYSIWYG editor
	new Jodit('.jodit_descricao_formal');
	new Jodit('.jodit_descricao_tecnica');
	new Jodit('.jodit_solucao');
	new Jodit('.jodit_resultados');

	// update subcategories
	$('[name="categoria"]').change(function() {
		$.ajax({
			url: '../../tarefas/get-subcategoria',
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

		if(val == '2') {
			$('.member-resp-field').show();			
		}
		else {
			$('.member-resp-field').hide();	
		}

		if(val == '1') {
			$('.tempo-previsto').show();
			var id_subcategoria = $('[name="subcategoria"]').val();
			var id_categoria = $('[name="categoria"]').val();
			
			if(id_categoria != "" && id_subcategoria != "")
				$('#average-time-info').fadeIn();	
		}
		else {
			$('.tempo-previsto').hide();
			$('#average-time-info').hide();	
		}
	});

	// update team list
	$('[name="projeto"]').change(function() {
		$.ajax({
			url: '../../tarefas/get-team',
			method: 'POST',
			dataType: 'html',
			data: { id: $(this).val()},
			success: function(json) {
				$('[name="responsavel_membro_tarefa"] option').remove();
				$('[name="responsavel_membro_tarefa"]').append(json);
			}
		});
	})

	// get average time for tasks from same subcategory
	$('[name="subcategoria"]').change(function() {
		var id_categoria = $('[name="categoria"]').val();
		var id_subcategoria = $(this).val();
		var nome = $('[name="subcategoria"]').find(":selected").text();
		
		$.ajax({
			url: '../../tarefas/get-average-time',
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
	});

	// set mask for HH:MM
	$('.mask-horario').mask('00h00min');

	// switch between category boxes (dev | test)
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

	// submit form 
	$('#formEditTarefa').unbind("submit").bind("submit", function(e){
		e.preventDefault();
		var form = $(this);
		
		if(validateForm("#formEditTarefa")) {
			$.ajax({
				url: form.attr("action"),
				method: 'POST',
				dataType: 'json',
				data: form.serializeArray(),
				success: function(json) {
					alert('Tarefa Editada com sucesso!');
					window.location.reload(true);
				},
				error: function() {
					alert('Erro na requisição AJAX!');
				}
			});				
		}
		else alert("Preencha os campos obrigatórios");
	});
});