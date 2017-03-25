$(document).ready(function() {
	var offset = 555;

	if($(window).scrollTop() >= offset) {
		$('header nav').addClass('nav-shrink');
	}
	else $('header nav').removeClass('nav-shrink');
	
	$(window).scroll(function() {
		if($(this).scrollTop() >= offset) {
			$('header nav').addClass('nav-shrink');
		}
		else $('header nav').removeClass('nav-shrink');
	});
});