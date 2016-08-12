function initialize() {

	var lat = -30.0468011;
	var long = -51.2293512;

  	var mapProp = {
	    center:new google.maps.LatLng(lat, long),
		zoom: 18,
	    mapTypeId:google.maps.MapTypeId.ROADMAP
	};

	var map = new google.maps.Map(document.getElementById("googleMaps"),mapProp);

	var marker = new google.maps.Marker({
		position:new google.maps.LatLng(lat, long),
	});

	marker.setMap(map);

	var infowindow = new google.maps.InfoWindow({
		content:"<strong>Av. Borges de Medeiros, 2253 / 601 - Centro Histórico</strong><br/>Porto Alegre - RS"
	});

	google.maps.event.addListener(marker, 'click', function() {
	  	infowindow.open(map,marker);
	});
}

google.maps.event.addDomListener(window, 'load', initialize);