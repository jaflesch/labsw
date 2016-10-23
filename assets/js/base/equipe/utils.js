$(document).ready(function() {
	// real time HEXCOLOR input
	$('body').on("keyup", '[name="identidade_visual"]', function(){
		$('#color-help span').css("background", $(this).val());
	});
});

// ajax call helper
function loadAll() {
	$.ajax({
		url: '../../equipe/getlist',
		method: 'POST',
		dataType: 'html',
		data: { id : $('[name="id_projeto"]').val() },
		success: function(data) {
			$('table tbody tr').remove();
			$('table tbody').append(data);
		}
	});
}