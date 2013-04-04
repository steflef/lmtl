angular.module('appMain', ['ngSanitize'])

//    .config(function ($routeProvider, $locationProvider) {
//        //$locationProvider.html5Mode(true);
//        $routeProvider.
//            when('/', {controller:HomeCtrl}).
//            when('/details/:idCall', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/details.html", controller:DetailsCtrl}).
//            when('/home', {controller:HomeCtrl}).
//            otherwise({redirectTo:'/home'});
//    })

    .directive('toolbar', function () {
            var linker = function(scope, element, attrs) {
                scope.$broadcast('newMenu', {id:'tb'});
            };

            return{
                restrict:'E',
                replace: true,
                templateUrl:'./public/js/ng/tmpl/toolbar_partial.html',
                link:linker
            }
        })

    .directive('map', function () {

        var linker = function(scope, element, attrs) {
            //console.log(scope);
            scope.map = new L.Map(attrs.id, {'scrollWheelZoom':false});
            scope.map.attributionControl.setPrefix('');
            scope.markersLayer = new L.LayerGroup();
            scope.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttrib = 'Map data © openstreetmap contributors',
                osm = new L.TileLayer(osmUrl, {minZoom:8, maxZoom:18, attribution:osmAttrib}),
                mapCenter = new L.LatLng(origin.lat, origin.lon);

            scope.map.setView(mapCenter, 13)
                .addLayer(osm);

            //jQuery stuff
            // Todo Convert to Angular Model, DOM oriented right now :(
            var mapStretch = {
                min:function () {
                    $(".mapColumn").removeClass("maximazer");
                    $(".contentColumns").removeClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-up").addClass("icon-chevron-down");

                    self.map.panBy(new L.Point(0, 100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.max();
                    });
                },
                max:function () {
                    $(".mapColumn").addClass("maximazer");
                    $(".contentColumns").addClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

                    self.map.panBy(new L.Point(0, -100));
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
    })
    .controller('CollectCtrl', function($rootScope, $scope, $http) {

        $scope.safeApply = function(fn) {
            var phase = this.$root.$$phase;
            if(phase == '$apply' || phase == '$digest') {
                if(fn && (typeof(fn) === 'function')) {
                    fn();
                }
            } else {
                this.$apply(fn);
            }
        };

        var self = $scope;
        $scope.datasets = [];
        $scope.places = [];
        $scope.place = [];

        $scope.init = function(){
            console.log('INIT');
            $scope.getDatasets();
        };

        $scope.test = function(){
            console.log($scope);
        };

        // LISTENERS
        $scope.$on('newDatasets', function ($scope, oData) {
            console.log('EVENT newDatasets');
            console.log(oData);
            self.datasets = oData.results;
            if(self.datasets.length < 1){
                console.log('No Place in Dataset!');
            }else{
                self.getPlaces( oData.results[0].id );
            }

        });

        $scope.$on('newPlaces', function ($scope, oData) {
            console.log('EVENT newPlaces');
            console.log(oData);
            self.places = oData.results;
            $rootScope.$broadcast("setMarkers", oData.results);
        });

        $scope.$on('newPlace', function ($scope, oData) {
            console.log('EVENT newPlace');
            console.log(oData);
            self.place = oData.results;

            self.safeApply();
        });

        // HTTP GET DATASET
        $scope.getDatasets = function(){
            console.log("Get Latest Dataset!!");
            var self = $scope;
            $http.get("./datasets").
                success(function(data, status) {
                    //console.log(status);
                    //console.log(data);

                    if(data.status == 200){
                        $scope.$broadcast('newDatasets', data);
                    }
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    self.status = status;
                });
        };

        // HTTP GET PLACES
        $scope.getPlaces = function(datasetId){
            console.log("Get Places!! Dataset("+datasetId+")");
            //var self = $scope;

            $http.get("./datasets/"+datasetId +"/places").
                success(function(data) {
                    //console.log(status);
                    //console.log(data);
                    if(data.status == 200){
                        self.$broadcast('newPlaces', data);
                    }
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    $scope.status = status;
                });
        };

        // HTTP GET SINGLE PLACE
        $scope.getPlace = function(placeId){
            console.log("Get Places #"+placeId);

            $http.get("./places/"+placeId ).
                success(function(data) {
                    //console.log(status);
                    //console.log(data);
                    if(data.status == 200){
                        self.$broadcast('newPlace', data);
                    }
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    $scope.status = status;
                });
        };

        // HTTP POST
/*        $scope.publish = function(){
            console.log("Publish in Stepper Scope!!");
            //console.log($scope);
            //console.log(self.$$childTail.uData);

            $http.post("./publish",{
                data: self.$$childTail.uData,
                headers: self.$$childTail.uHeaders,
                metadata: self.$$childTail.uMetadata
            }).
                success(function(data, status) {
                    console.log(status);
                    console.log(data);
                    $scope.status = status;
                    $scope.data = data;
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });
        };*/

        $scope.getScope = function(){
            console.log($scope);
        }

    });