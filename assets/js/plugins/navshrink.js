function shrink_navbar() {
  var width = $(window).width();
  var offset;
      
  if(width > 991)
    offset = 770;
  else 
    offset = 80;

  offset -= 80;
      
  if ($(document).scrollTop() >= offset) {
    $('nav').addClass('shrink');
  } else {
    $('nav').removeClass('shrink');
  }
}