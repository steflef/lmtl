angular.module('appDetails', ['ngSanitize'])

    .directive('map', function () {

        var linker = function(scope, element, attrs) {

            scope.map = new L.Map(attrs.id, {'scrollWheelZoom':false});
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttrib = 'Map data © openstreetmap contributors',
                osm = new L.TileLayer(osmUrl, {minZoom:8, maxZoom:18, attribution:osmAttrib}),
                mapCenter = new L.LatLng(origin.lat, origin.lon);

            scope.map
                .setView(mapCenter, 15)
                .addLayer(osm)
                .attributionControl.setPrefix('');
            var marker = L.marker(mapCenter).addTo(scope.map);

            // Todo Convert to Angular Model, DOM oriented right now :(
            var mapStretch = {
                min:function () {
                    $(".map-wrapper").removeClass("maximazer");
                    $(".map-wrapper").removeClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-up").addClass("icon-chevron-down");

                    scope.map.panBy(new L.Point(0, 100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.max();
                    });
                },
                max:function () {
                    $(".map-wrapper").addClass("maximazer");
                    $(".map-wrapper").addClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

                    scope.map.panBy(new L.Point(0, -100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.min();
                    });
                }
            };

            $(".map-pull").click(function () {
                mapStretch.max();
            });
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('streetview', function () {

        var linker = function(scope, element, attrs) {

            var myPos, myPOV;

            if(typeof(google.maps.StreetViewPanorama) === 'undefined'){
                console.log('noGoogle');
                $(element).hide();
                return false;
            }

            var target = new google.maps.LatLng(origin.lat,origin.lon);

            var panoramaOptions = {
                position: target,
                addressControl: false,
                linksControl: false,
                panControl: false,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL
                },
                pov: {
                    heading: 1,
                    pitch: 10,
                    zoom: 1
                }
            };

            var streetViewService = new google.maps.StreetViewService();
            var STREETVIEW_MAX_DISTANCE = 100;
            var latLng = new google.maps.LatLng(origin.lat,origin.lon);
            streetViewService.getPanoramaByLocation(latLng, STREETVIEW_MAX_DISTANCE, function (streetViewPanoramaData, status) {
                if (status === google.maps.StreetViewStatus.OK) {
                    panoramaOptions.position = streetViewPanoramaData.location.latLng;
                    var panorama = new google.maps.StreetViewPanorama(document.getElementById(attrs.id),panoramaOptions);
                } else {
                    //console.log("ERROR BAIL OUT!");
                    // no street view available in this range, or some error occurred
                    $(element).hide();
                }
            });
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('raoId', function () {

        var linker = function(scope, element, attrs) {

            scope.details.callId = $(element).text();
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('staredId', function () {

        var linker = function(scope, element, attrs) {

            //console.log($(element).text());
            scope.details.isStared = ($(element).text() !== '')?true:false;
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('typeahead', function(){

        return{
            restrict: 'A',
            link:function(scope, element, attrs) {

                $(element)
                    .typeahead({
                        source:function (query, process) {
                            return $.get(BASE_URL+'api/typeahead/query/format/json/q/' + query, null, function (data) {
                                return process(data.options);
                            });
                        }
                    })
                    .change(function () {
                        $(this).parent().submit();
                    });
            }
        }
    })
    .filter('sub', function () {
        return function (text, from, to) {
            if (isNaN(from))
                from = 0;

            if (isNaN(to))
                to = 1;

            if (text.length < to) {
                return text;
            }
            else {
                return String(text).substring(from, to);
            }
        }
    });