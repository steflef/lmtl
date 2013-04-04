function MapCtrl($rootScope, $scope, $filter, $http) {
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

    $scope.$on('setMarkers', function ($scope, Places) {
        //console.log($scope);
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

        _.each(Places, function(element, index){
            var title = element.name;
            var marker = new L.Marker(new L.LatLng(element.location.latitude, element.location.longitude), { title:title });
            marker.bindPopup(title);
            self.markers.addLayer(marker);
        });

        self.markers.on('click', function (a) {
            console.log(a);
            //$rootScope.$broadcast('setQueryById', {options:a.layer.options});
        });

        self.markers.on('clusterclick', function (a) {
            _.each(a.layer.getAllChildMarkers(), function (element, index, list) {
                //console.log(element.options.title );
            });
        });

        self.map.addLayer(self.markers);

        if (_.size(self.markers._layers) > 0) {

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
}