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
        $scope.place = {};
        $scope.datasets_panel = "easeIn";

        $scope.init = function(){
            console.log('INIT');
            //$scope.$broadcast("load","Connexion");
            $scope.$broadcast("showMsg",{title:"Initialisation",text:"Connexion au serveur"});
            //$scope.$broadcast("showMsg",{title:"Chargement",text:"test ..."});

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


        $scope.getScope = function(){
            console.log($scope);
        }

    });