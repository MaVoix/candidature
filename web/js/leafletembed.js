//leaflet
var map;

$(document).ready(function () {


    $(window).on('resize',function(){
        resizeMap()
    });
    resizeMap();
    initmap();
});

function resizeMap(){
    var $mapleaflet = $('#mapleaflet');
    if ($mapleaflet.length) {
        var $header = $('.navbar-fixed-top.topmenu');
        var $footer = $('.footer > .container');
        $mapleaflet.height($(window).height() - $header.height() - $footer.height());
        if($header.height()>60){
            $mapleaflet.css('margin-top','50px');
        }else{
            $mapleaflet.css('margin-top',0);
        }

    }
}

function initmap() {
    // set up the map
    map = new L.Map('mapleaflet');

    // create the tile layer with correct attribution
    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    var osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
    var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 8, attribution: osmAttrib});

    // start the map in South-East England
    map.setView(new L.LatLng(43, 0), 3);
    map.addLayer(osm);


    var MavoixIcon = L.icon({
        iconUrl: '/css/images/marker-map.png',
        iconSize:     [50, 42], // size of the icon
        iconAnchor:   [25, 21], // point of the icon which will correspond to marker's location
        popupAnchor:  [0, -30] // point from which the popup should open relative to the iconAnchor
    });

    for(var k=0;k<markerList.length;k++){
       var marker=L.marker([markerList[k][0], markerList[k][1]], {icon: MavoixIcon}).addTo(map);
        marker.bindPopup(markerList[k][2]);
    }

}