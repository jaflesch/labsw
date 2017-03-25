function myMap() {
    var latitude = -30.04855;
    var longitude = -51.186682;

    var mapCanvas = document.getElementById("gmap");
    var mapOptions = {
        center: new google.maps.LatLng(latitude, longitude),
        zoom: 17
    };

    var myCenter = new google.maps.LatLng(latitude - 0.000515, longitude - 0.0005115);
    var marker = new google.maps.Marker({position: myCenter});

    var infowindow = new google.maps.InfoWindow({
	    content: "<strong>EcoProdutiva</strong><br/> Rua Chile, 841 - Petrópolis / POA"
  	});

    var map = new google.maps.Map(mapCanvas, mapOptions);
    marker.setMap(map);
  	infowindow.open(map,marker);
}