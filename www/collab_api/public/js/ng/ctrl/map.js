function MapCtrl($rootScope, $scope, $compile, $filter, $http) {
    $scope.test = "supreDUper";
    self = $scope;
    root = $rootScope;

   $scope.setGeoFilter = function(feature){

       $rootScope.$broadcast('setGeoFilter', feature);
    };

    $rootScope.$on('mapDraw', function ($foragectrlScope, feature) {

        if(feature.geometry === undefined) return false;

        if(feature.geometry.type === "Polygon"){

            var polygon = L.polygon(feature.geometry.coordinates);
            self.drawnItems.addLayer(polygon);
        }

        if(feature.geometry.type === "Point"){

            var circle = L.circle(feature.geometry.coordinates,feature.properties.radius);
            self.drawnItems.addLayer(circle);
        }
        //console.log(self.map);
    });

    $scope.$on('setMarkers', function ($scope, oData) {
        var Places = oData.Places;
        var zoomToBounds = oData.zoomToBounds || false;
        console.log(zoomToBounds);
        //console.log(Places);
        if (self.map.hasLayer(self.markers)) {
            self.map.removeLayer(self.markers);
            self.markers = null;
        }
/*        var _filteredCalls = [];
        var subCalls = $filter('filter')(obj.evnts, obj.query);
        _.each(subCalls, function (element,index) {
            if (element.lg !== undefined && element.lg !== '') {

                _filteredCalls.push([parseFloat(element.lt), parseFloat(element.lg), element.id]);
            }
        });*/

        self.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});

        _.each(Places, function(element){
            var title = element.label;
            var id = element.id;
            var latlng = new L.LatLng(element.location.latitude, element.location.longitude);

            var marker = new L.Marker(latlng, { title:title, latlng:latlng, id:id });
            //marker.bindPopup(title);
            self.markers.addLayer(marker);
        });

        self.markers.on('click', function (a) {

            var point = new L.Point(0, -40);
            var content = '<p>'+a.layer.options.title+'<br>';
            content += '<test rel="'+a.layer.options.id+'"><a href="#" class="pseudo-btn"><span class="label">Afficher la fiche détaillée</span></a></test>';
            content += '</p>';
            var popup = L.popup({offset:point})
                .setLatLng(a.layer.options.latlng)
                .setContent(content)
                .openOn(self.map);

            $compile($(".leaflet-popup-content"))(self);
        });

        self.markers.on('clusterclick', function (a) {
/*            _.each(a.layer.getAllChildMarkers(), function (element, index, list) {
                //console.log(element.options.title );
            });*/
        });

        self.map.addLayer(self.markers);

        // Zoom to bounds
        if ( (_.size(self.markers._layers) > 0) && zoomToBounds){
            console.log('ZOOM TO BOUNDS');
            var t_bounds = [];
            _.each(self.markers._layers, function (element, index, list) {

                t_bounds.push(new L.LatLng(element._latlng.lat, element._latlng.lng));
            });
            if (t_bounds.length == 1) {
                console.log("Set View");
                self.map.setView(t_bounds[0], 15);
            } else {
                if (t_bounds.length >= 1) {
                    var bounds = new L.LatLngBounds(t_bounds);
                    //console.log(t_bounds);
                    self.map.fitBounds(bounds);
                }
            }

        }

        root.$broadcast("marked");
    });

    $scope.$on('setMapCenter', function ($scope, Point) {
        var location = new L.LatLng(Point.lat, Point.lon);

        self.map.setView(location, 18);
    });

    $scope.$on('getMapCenter', function ($scope, Point) {
        root.$broadcast("newMapCenter",   {LatLng:self.map.getCenter(),id:Point.id});
    });

    $scope.$on('geoLocation', function(){
        self.map.stopLocate();
        var locate = self.map.locate({
            setView: true,
            maxZoom: 18,
            maximumAge: 3000,
            enableHighAccuracy: true,
            watch: true
        });
        console.log(locate);
    });

    $scope.showDetails = function(id){
        console.log("test");
        console.log(id);
        console.log(arguments);
        $rootScope.$broadcast("markerShowDetails", {id:id});
    }
}