window["_GOOG_TRANS_EXT_VER"] = "1";
window["_GOOG_TRANS_EXT_VER"] = "1";

var map, cloudmade, mapquestOSM, mapquestOAM, clusterMarkers, markers;
$.ajaxSetup({
    cache: false
});

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

function showForm() {
    $('#map').css('display', 'none');
    $('.crosshair').css('display', 'none');
    $('#mark_button').css('display', 'none');
    $('body').css('overflow', 'auto');
    $('#form').css('display', 'block');
}

function hideForm() {
    $('#map').css('display', 'block');
    $('.crosshair').css('display', 'block');
    $('#mark_button').css('display', 'block');
    $('body').css('overflow', 'hidden');
    $('#form').css('display', 'none');
}

function markLocation() {
    $('#newmarkerModal').modal('show');
    $('#lat').val(map.getCenter().lat);
    $('#lng').val(map.getCenter().lng);
}

function submitForm() {
    $('#newmarkerModal').modal('hide');
    $("#loading-mask").show();
    $("#loading").show();
    var name = $('#name').val();
    var comment = $('#comment').val();
    var mood = $('#mood').val();
    var lat = $('#lat').val();
    var lng = $('#lng').val();
    var dataString = {
        "name": name,
        "comment": comment,
        "mood": mood,
        "lat": lat,
        "lng": lng
    };
    $.ajax({
        type: "POST",
        url: "point_insert.php",
        data: dataString,
        success: function(data) {
            $('#gform')[0].reset();
            var location = new L.LatLng(lat, lng);
            var marker = new L.Marker(location, {
                title: name
            });
            var content = '<table class="table table-striped table-bordered table-condensed table-hover">' + '<tr>' + '<th>Name: </th>' + '<td>' + name + '</td>' + '</tr>' + '<th>Comment: </th>' + '<td>' + comment + '</td>' + '</tr>' + '</tr>' + '<th>Mood: </th>' + '<td>' + mood + '</td>' + '</tr>' + '</table>';
            marker.bindPopup(content, {
                maxWidth: '200',
                maxHeight: '200'
            });
            clusterMarkers.addLayer(marker);
            $('#markerTable').append($('<tr><td><a class="btn btn-small btn-inverse" href="#" onclick="map.setView([' + lat + ', ' + lng + '], 17); $(\'#markersModal\').modal(\'hide\'); return false;"><i class="icon-map-marker icon-white"></i></a></td><td>' + name + '</td><td>' + comment + '</td></tr>'));
            $("#loading-mask").hide();
            $("#loading").hide();
        }
    });
    return false;
}

cloudmade = new L.TileLayer("http://{s}.tile.cloudmade.com/9b1705f449fe4d48a70563eac6eb921e/997/256/{z}/{x}/{y}.png", {
    maxZoom: 18,
    subdomains: ["a", "b", "c"],
    attribution: 'Map data (c) <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors, CC-BY-SA.'
});
mapquestOSM = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {
    maxZoom: 18,
    subdomains: ["otile1", "otile2", "otile3", "otile4"],
    attribution: 'Basemap tiles courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">. Map data (c) <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors, CC-BY-SA.'
});
mapquestOAM = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.jpg", {
    maxZoom: 18,
    subdomains: ["oatile1", "oatile2", "oatile3", "oatile4"],
    attribution: 'Tiles courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a>. Map data (c) <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> contributors, CC-BY-SA.'
});

markers = new L.FeatureGroup();
clusterMarkers = new L.MarkerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true
});

function getData() {
    markers.clearLayers();
    $.getJSON("get_data.php", function(data) {
        for (var i = 0; i < data.length; i++) {
            var location = new L.LatLng(data[i].lat, data[i].lng);
            var name = data[i].name;
            var comment = data[i].comment;
            var mood = data[i].mood;
            var marker = new L.Marker(location, {
                title: name
            });
            var content = '<table class="table table-striped table-bordered table-condensed table-hover">' + '<tr>' + '<th>Name: </th>' + '<td>' + name + '</td>' + '</tr>' + '<th>Comment: </th>' + '<td>' + comment + '</td>' + '</tr>' + '</tr>' + '<th>Mood: </th>' + '<td>' + mood + '</td>' + '</tr>' + '</table>';
            var navbarHeight = $('.navbar').height();
            marker.bindPopup(content, {
                maxWidth: '200',
                maxHeight: '200',
                autoPanPadding: new L.Point(5, navbarHeight + 5)
            });
            //markers.addLayer(marker);
            clusterMarkers.addLayer(marker);
            $('#markerTable').append($('<tr><td><a class="btn btn-small btn-inverse" href="#" onclick="map.setView([' + data[i].lat + ', ' + data[i].lng + '], 17); $(\'#markersModal\').modal(\'hide\'); return false;"><i class="icon-map-marker icon-white"></i></a></td><td>' + name + '</td><td>' + comment + '</td></tr>'));
        }
    }).complete(function() {
            //map.fitBounds(markers.getBounds());
            map.fitBounds(clusterMarkers.getBounds());
        });
}

map = new L.Map("map", {
    //zoom: 15,
    //center: new L.LatLng(41.06911170443168, -89.28026676177979),
    layers: [mapquestOSM, /*markers*/ clusterMarkers]
});
getData();
map.attributionControl.setPrefix('');
map.fitBounds([
    [49.384358, -66.949895],
    [24.544701, -124.733174]
]);

function onLocationFound(e) {
    /*var radius = e.accuracy / 2;
     L.marker(e.latlng).addTo(map)
     .bindPopup("You are within " + radius + " meters from this point").openPopup();
     L.circle(e.latlng, radius).addTo(map);*/
    //console.log(e.accuracy);
    if (e.accuracy <= 20) {
        map.stopLocate();
    };
    var killGPS = setInterval(function() {
        map.stopLocate();
        clearInterval(killGPS);
    }, 10000);

}

function onLocationError(e) {
    alert(e.message);
}

map.on('locationfound', onLocationFound);
//map.on('locationerror', onLocationError);

//map.locate({setView: true, maxZoom: 16});
// GeoLocation Control


function geoLocate() {
    map.stopLocate();
    map.locate({
        setView: true,
        maxZoom: 17,
        maximumAge: 3000,
        enableHighAccuracy: true,
        watch: true
    });
}
var geolocControl = new L.control({
    position: 'topleft'
});
geolocControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-control-zoom leaflet-control');
    div.innerHTML = '<a class="leaflet-control-geoloc" href="#" onclick="geoLocate(); return false;" title="My location"></a>';
    return div;
};
//map.addControl(geolocControl);
// Layer control
var baseLayers = {
    "Streets": mapquestOSM,
    "Imagery": mapquestOAM
};
var overlays = {
    "<img height='20' width='12' src='assets/leaflet/images/marker-icon.png'>&nbsp;Markers": /*markers*/
        clusterMarkers
};
if (isMobile.any() || document.body.clientWidth <= 767) {
    var isCollapsed = true;
} else {
    var isCollapsed = false;
};
var layersControl = new L.Control.Layers(baseLayers, overlays, {
    collapsed: isCollapsed,
    autoZIndex: true
});
map.addControl(layersControl);

map.on('movestart', function() {
    $('#crosshair').css('opacity', '1');
});
map.on('moveend', function() {
    $('#crosshair').css('opacity', '0.5');
});
map.on('popupopen', function() {
    $('#crosshair').css('display', 'none');
});
map.on('popupclose', function() {
    $('#crosshair').css('display', 'block');
});