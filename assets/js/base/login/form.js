$(document).ready(function(){

	$("#formLogin").unbind("submit").bind("submit", function(e){
		
		var form = $(this);
		e.preventDefault();
		
		$.ajax({
			url: form.attr("action"),
			method: "POST",
			dataType: 'json',
			data: form.serializeArray(),
			success: function(json) {
				if(json.success) {
					$('.help').hide();
					window.location.reload(true);
				}
				else {
					$('.help').show();
					$('.fa-lock').css("color", "#a94442");
					$('[name="password"]').addClass("alert-danger").focus();
				}
			},
			error : function() {
				alert("Erro na requisição AJAX!");
			}
		})
	});

	$('#limpar').click(function() {
		$('input[type="text"],input[type="password"]').val("");
	});
});