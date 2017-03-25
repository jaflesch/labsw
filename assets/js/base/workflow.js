$(document).ready(function() {
	$('button.show').click(function() {
		if($(this).hasClass('focus')) {
			$(this).siblings('.complete-text').slideUp();
			$(this).siblings('.abstract-text').show();
		}
		else {
			$(this).siblings('.abstract-text').hide();
			$(this).siblings('.complete-text').slideDown();
		}
		
		$(this).toggleClass('focus');
		$(this).blur();
	});
});