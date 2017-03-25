$(document).ready(function() {
	$('#formLogin').unbind("submit").bind("submit", function(e) {		
		e.preventDefault();
		var form = $(this);
		
		if($('[name="login"]').val() == "" || $('[name="password"]').val() == "") {
			alert('Por favor, preencha ambos os campos do formulário!');
		}
		else {
			$.ajax({
				url: form.attr('action'),
				method: 'POST',
				dataType: 'json',
				data: form.serializeArray(),
				success: function(json) {
					if(json.success)
						document.location = "home";
					else 
						alert(json.msg);
				}
			});
		}
	});
});