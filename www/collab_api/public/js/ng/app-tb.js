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
            scope.map = new L.Map(attrs.id, {'scrollWheelZoom':false, left:"340px"});
            scope.map.attributionControl.setPrefix('');
            scope.markersLayer = new L.LayerGroup();
            //scope.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});
            scope.markers = new L.MarkerClusterGroup({ showCoverageOnHover: false, animateAddingMarkers : true });
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttrib = 'Map data © openstreetmap contributors',
                osm = new L.TileLayer(osmUrl, {minZoom:8, maxZoom:18, attribution:osmAttrib}),
                mapCenter = new L.LatLng(origin.lat, origin.lon);

            scope.map.setView(mapCenter, 13)
                .addLayer(osm);

            //jQuery stuff
            // Todo Convert to Angular Model, DOM oriented right now :(
/*            var mapStretch = {
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
            });*/
        };

        return{
            restrict:'A',
            link:linker
        }
    })

    .directive('updatemodelonblur', function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, elm, attr, ngModelCtrl)
            {
                if(attr.type === 'radio' || attr.type === 'checkbox')
                {
                    return;
                }

                // Update model on blur only
                elm.unbind('input').unbind('keydown').unbind('change');
                var updateModel = function()
                {
                    scope.$apply(function()
                    {
                        ngModelCtrl.$setViewValue(elm.val());
                    });
                };
                elm.bind('blur', updateModel);

                // Not a textarea
                if(elm[0].nodeName.toLowerCase() !== 'textarea')
                {
                    // Update model on ENTER
                    elm.bind('keydown', function(e)
                    {
                        e.which == 13 && updateModel();
                    });
                }
            }
        };
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

        $scope.isMobile = {
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
                return (self.isMobile.Android() || self.isMobile.BlackBerry() || self.isMobile.iOS() || self.isMobile.Opera() || self.isMobile.Windows());
            }
        };

        $scope.datasets = [];
        $scope.places = [];
        $scope.place = {};
        $scope.cat = {};
        $scope.datasets_panel = "easeIn";

        $scope.init = function(){
            console.log('INIT');
            //$scope.$broadcast("load","Connexion");
            $scope.$broadcast("showMsg",{title:"Initialisation",text:"Connexion au serveur"});
            //$scope.$broadcast("showMsg",{title:"Chargement",text:"test ..."});
            console.log( self.isMobile.any());
            $scope.getDatasets();
        };

        $scope.test = function(){
            console.log($scope);
        };

        $scope.showDatasets = function(){
            $scope.datasets_panel = "easeIn";
        }
        $scope.hideDatasets = function(){
            $scope.datasets_panel = "easeOut";
        }
        $scope.showLoader = function(text){
            if($scope.overlay){
                return false;
            }

            var opts = {
                lines: 13, // The number of lines to draw
                length: 11, // The length of each line
                width: 5, // The line thickness
                radius: 17, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                color: '#FFF', // #rgb or #rrggbb
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: 'auto', // Top position relative to parent in px
                left: 'auto' // Left position relative to parent in px
            };

            var target = document.createElement("div");
            target.id = "overlay";
            target.style.cssText = 'background-color:#fff;position:fixed;top:0;bottom:0;width:100%;z-index:2;opacity:.5;';
            document.body.appendChild(target);
            var spinner = new Spinner(opts).spin(target);
            $scope.backdrop = target;
            $scope.overlay = iosOverlay({
                text: text,
                duration: null,
                spinner: spinner
            });
            return false;
        };

        $scope.hideLoader = function(){
            if(!$scope.overlay){
                return false;
            }
            $scope.overlay.hide();
            $scope.overlay = null;

            var handle = $scope.backdrop;
            handle.parentNode.removeChild(handle);
            return true;
        };

        $scope.showMsg = function(msg){
            console.log("showMsg");
            if($scope.overlayMsg){
                return false;
            }

            var target = document.createElement("div");
            target.id = "overlay";

            var uiOverlay = document.createElement("div");
            uiOverlay.className = "ui-overlay";

            var msgBox = document.createElement("div");
            msgBox.className = "ui-msgbox";

            var msgTitle = document.createElement("div");
            msgTitle.className = "title lead";
            var newContent = document.createTextNode(msg.title);

            var p = document.createElement("p");
            var pContent = document.createTextNode(msg.text);
            p.appendChild(pContent);

            msgTitle.appendChild(newContent);
            msgTitle.appendChild(p);

            msgBox.appendChild(msgTitle);

            target.appendChild(msgBox);
            target.appendChild(uiOverlay);
            document.body.appendChild(target);

            $scope.overlayMsg = target;
        };

        $scope.hideMsg = function(){
            console.log("HIDE!");
            if(!$scope.overlayMsg){
                return false;
            }
            //$scope.overlayMsg.hide();
            var handle = $scope.overlayMsg;
            handle.parentNode.removeChild(handle);
            $scope.overlayMsg = null;
            return true;
        };

        // LISTENERS
        $scope.$on('load', function () {
            var text = arguments[1] || "";
            self.showLoader(text);
        });

        $scope.$on('loadEnd', function () {
            self.hideLoader();
        });

        $scope.$on('showMsg', function () {
            var msg = arguments[1] || {title:"",text:""};
            var flash = arguments[2] || false;
            self.showMsg(msg);

            if(flash){
                setTimeout(function(){
                        self.hideMsg();
                    },3000
                );
            }
        });

        $scope.$on('hideMsg', function () {

            self.hideMsg();
        });

        $scope.$on('newMapCenter', function ($scope, Point) {
            self.place.location.latitude = Point.LatLng.lat;
            self.place.location.longitude = Point.LatLng.lng;

            var tempPlace = _.find(self.places, function(item){ return item.id == Point.id; });
            console.log(tempPlace);
            tempPlace.location.latitude = Point.LatLng.lat;
            tempPlace.location.longitude = Point.LatLng.lng;

            $rootScope.$broadcast("setMarkers", self.places);

            self.$broadcast("hideMsg");
        });

        $scope.$on('newDatasets', function ($scope, oData) {

            console.log('EVENT newDatasets');
            console.log(oData);
            self.datasets = oData.results;
            if(self.datasets.length < 1){
                console.log('No Dataset!');
                self.$broadcast("showMsg",{title:"Attention",text:"Aucun jeu de données disponible"});
            }else{
                //self.$broadcast("loadEnd");
                self.$broadcast("hideMsg");
                self.getCategories();
                //self.getPlaces( oData.results[0].id );
            }
        });

        $scope.$on('newPlaces', function ($scope, oData) {

            console.log('EVENT newPlaces');
            console.log(oData);

            self.hideDatasets();
            self.safeApply();
            self.places = oData.results;
            $rootScope.$broadcast("setMarkers", oData.results);
            self.$broadcast("hideMsg");


        });

        $scope.$on('marked', function () {
            console.log('MARKED');
        });

        $scope.$on('newPlace', function ($scope, oData) {
            console.log('EVENT newPlace');
            console.log(oData);
            self.place = oData.results;
            self.$broadcast("hideMsg");

            self.safeApply();

            //Categories
            var selectedCategorie = [];
            var primaryId = self.place.categories.primary_category.id;
            var secondaryId = self.place.categories.secondary_category.id;
            _.each(self.cat.options, function(item){
                //console.log(item.id + " ==? "+ self.place.categories.primary_category.id);

                if(item.id == primaryId ){
                    selectedCategorie[0] = item;
                }
                if(item.id == secondaryId){
                    selectedCategorie[1] = item;
                }
            });
            console.log("Categories");
            console.log(selectedCategorie);
            console.log("-----------------");
            self.selectedCategorie = selectedCategorie;

        });

        // HTTP GET DATASET
        $scope.getDatasets = function(){

            var self = $scope;
            self.$broadcast("showMsg",{title:"Chargement",text:"Jeux de données"});
            console.log("Get Latest Dataset!!");

            $http.get("./datasets").
                success(function(data, status) {

                    //self.$broadcast("hideMsg");
                    if(data.status == 200){
                        $scope.$broadcast('newDatasets', data);
                    }
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    self.status = status;
                    //self.$broadcast("loadEnd");
                    self.$broadcast("showMsg",{title:"Attention",text:"Status: "+status});
                });
        };

        // HTTP GET PLACES
        $scope.getPlaces = function(datasetId){

            var self = $scope;
            self.$broadcast("showMsg",{title:"Connexion au serveur",text:"Chargement des lieux en cours"});
            console.log("Get Places!! Dataset("+datasetId+")");

            $http.get("./datasets/"+datasetId +"/places").
                success(function(data) {

                    if(data.status == 200){
                        self.$broadcast('newPlaces', data);
                    }
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    $scope.status = status;

                    self.$broadcast("showMsg",{title:"Attention",text:"Status: "+status});
                });
        };

        // HTTP GET SINGLE PLACE
        $scope.getPlace = function(placeId){
            console.log("Get Places #"+placeId);
            var self = $scope;
            //self.$broadcast("showMsg",{title:"Connexion au serveur",text:"Chargement des informations en cours"});
            self.place = [];

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

        $scope.getCategories = function(){
            console.log("Get Categories");
            var self = $scope;
            self.$broadcast("showMsg",{title:"Connexion au serveur",text:"Chargement des catégories en cours"});
            $http.get("./categories").
                success(function(data) {
                    $scope.cat.options = data.results;
                    self.$broadcast("hideMsg");
                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                });
        }

        $scope.setMapCenter = function(){
            $rootScope.$broadcast("setMapCenter", {lon:self.place.location.longitude,lat:self.place.location.latitude});
        }

        $scope.getMapCenter = function(){
            self.$broadcast("showMsg",{title:"Calcul en cours",text:"Position du lieux"});
            $rootScope.$broadcast("getMapCenter", {id:self.place.id});
        }

        $scope.reverseGeocoding = function(){
            // reverse geocode with Google
            if (typeof self.geocoder === 'undefined') {
                self.geocoder = new google.maps.Geocoder();
            }


            var location = new google.maps.LatLng(self.place.location.latitude, self.place.location.longitude);
            self.geocoder.geocode( {'latLng': location}, function(results, status) {

                if (status == google.maps.GeocoderStatus.OK) {
                    //var lon = results[0].geometry.location.lng();
                    //var lat = results[0].geometry.location.lat();
                    var location_type = results[0].geometry.location_type;
                    var address_components = results[0].address_components;
                    var formatted_address = results[0].formatted_address;
                    var postal_code ="";
                    var city = "";
                    var street_number = "";
                    var route = "";

                    _.each(address_components, function(item){
                        console.log(item.types[0] + " >> " + item.long_name);
                        if( item.types[0] === "postal_code"){
                            self.place.location.postal_code = item.long_name;
                        }

                        if( item.types[0] === "locality"){
                            self.place.location.city = item.long_name;
                        }

                        if( item.types[0] === "street_number"){
                            street_number = item.long_name;
                        }

                        if( item.types[0] === "route"){
                            route = item.long_name;
                        }
                    });

                    self.place.location.address = street_number + " " + route;
                    self.place.location.service = "Google";
                    self.place.location.location_type = location_type;
                    self.safeApply();
/*                    var location = {

                        "location_type" : location_type,
                        "formatted_address" : formatted_address,
                        "postal_code" : postal_code,
                        "city" : city,
                        "service" : "Google"
                    };*/
                    //console.log(location);

/*                    self.uData.features[targetRow].geometry.coordinates = [lon,lat];
                    self.uData.features[targetRow]._geo = location;

                    self.geoConsole = "ID:"+targetRow+" ["+adr+"] : ("+lon+","+lat+")";
                    self.geoLog += "\n" + self.geoConsole;
                    self.safeApply();
                    timer = 800;
                    self.geoIndex = pos;
                    responseCount ++;
                    pos ++;*/

                } else
                {
                    console.log(status);
                }
            });
        }

        $scope.geoLocation = function() {
            $rootScope.$broadcast("geoLocation");
        }


        $scope.getScope = function(){
            console.log($scope);
        }

    });