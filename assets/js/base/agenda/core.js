$(document).ready(function(){

	$("body").on("click", ".arrows", function(e){
		var date = $(this).data('date-prev');
		date = date === undefined ? $(this).data('date-next') : date;

		$.ajax({
			url: 'agenda/get-calendar',
			method: "POST",
			dataType: 'json',
			data: { 
				data : date, 
				orientation : $(this).hasClass('fa-angle-left') ? 'left' : 'right'
			},
			success: function(json) {
				$('.calendar-box').html(json.calendar);
				$('.fa-angle-left').data('date-prev', json.prev);
				$('.fa-angle-right').data('date-next', json.next);

				$('.event-list li').remove();
				$('.event-list').append(json.list);
				$('#data-titulo').text(json.data_extenso);
			},
			error : function() {
				alert("Erro na requisição AJAX!");
			}
		})
	});
});