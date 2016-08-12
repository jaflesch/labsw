!function(){

	$('#formContato').unbind('submit').bind('submit', function(e){
		e.preventDefault();
		var $form = $(this);
		
		$.ajax({
		  	url: $form.attr('action'),
		  	method: 'POST',
		  	dataType: 'json',
		  	data: $form.serializeArray(),
		  	success: function(resp) {
		  		if(resp.success) {
		  			alert(resp.msg);
		  		}
		  	},
		  	error: function(resp) {
		  		alert('Erro na requisição AJAX!');
		  	}		
		});
	});
}();