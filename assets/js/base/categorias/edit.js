$(document).ready(function() {

	$('#formAddSubcategoria').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$('table tbody tr.no-subcategory').remove();
					$('table tbody').append("<tr><td>" + $('#formAddSubcategoria [name="nome"]').val() + "</td><td><button data-id='" + json.id + "' class='btn btn-danger btn-remove'> Excluir </button></td></tr>");
				}
			}
		})
	});

	$('#editCategoria').unbind("submit").bind("submit", function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			url: form.attr("action"),
			method: 'POST',
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$('.task-completed-notification').fadeIn('fast');
					setTimeout(function(){
						$('.task-completed-notification').fadeOut('fast');
					}, 2000);
				}
			}
		})
	});

	$('body').on("click", '.btn-remove', function() {
		var id = $(this).data("id");
		var url = $('#formAddSubcategoria').attr("action").split("add-subcategoria")[0] + "remove-subcategoria";
		
		$.ajax({
			url: url,
			method: 'POST',
			dataType: 'json',
			data: { id : id },
			success: function(json) {
				if(json.success) {
					$('table tbody button[data-id="'+ id +'"]').parent().parent().remove();
				}
				if($('table tbody tr').length === 0 ) {
					$('table tbody').append("<tr class='no-subcategory'><td> Nenhuma subcategoria registrada ainda </td><td></td></tr>");
				}
			}
		})
	});
});