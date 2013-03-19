angular.module('appMain', ['ngSanitize','ngUpload'])

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
            scope.$broadcast('newMenu', {id:'upload'});
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
        };

        return{
            restrict:'A',
            link:linker
        }
    })

    .directive('chosenRao', function () {
        var linker = function(scope, element) {
            scope.$watch('cat.options', function(item){
                //console.log("Options Watcher");
                if(scope.cat.hash.lenght !== 0 ){
                    var codesList = _.pluck(scope.cat.options, 'id');
                    var indexes = [];
                    _.each(scope.cat.hash, function (code) {
                        indexes.push(_.indexOf(codesList, code));
                    });
                    $(element).val(indexes).trigger("liszt:updated");
                }else{
                    $(element).trigger('liszt:updated');
                }
            },true);

            var _scope = scope;
            //console.log(element);
            $(element).chosen().change( function(event, item){

                scope.safeApply(function(){
                //scope.$apply(function(){
                    var hash = _scope.cat.hash;

                    if( item.selected !== undefined ){
                        hash.push(_scope.cat.options[item.selected].id);
                    }
                    if( item.deselected !== undefined ){
                        var id= _scope.cat.options[item.deselected].id;
                        hash.splice(_.indexOf(hash,id),1);
                    }
                })

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
    .filter('inst', function () {
        return function (text) {
            return typeof text;
        }
    })
    .controller('StepperCtrl', function($rootScope, $scope, $http, $location, $anchorScroll) {

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

        $scope.maxSteps = 5;
        $scope.step = 1;
        $scope.check = {
            geo:    false,
            name:   false,
            description: false,
            source: false,
            categories: false,
            right:  false,
            licence: false
        };
        $scope.certification = {
            right:  false,
            licence: false
        };
        $scope.validationAlert = {
            visibility:0,
            msg:""
        };

        // Listeners
        $scope.$on('nextStep', function ($scope) {
            if(self.step + 1 <= self.maxSteps){
                self.step ++;
                self.changed();
            }
        });

        $scope.$on('prevStep', function ($scope) {
            if(self.step - 1 > 0){
                self.step --;
                self.changed();
            }
        });

        $scope.$on('toStep', function ($scope, param) {
            self.step = param.step;
            self.changed();
        });

        $scope.$on('clearData', function ($scope) {
            self.step = 1;
            self.changed();
        });

        $scope.$on('newData', function ($scope, oData) {
            self.toStep(2);
        });

        $scope.$on('newStep', function ($scope, stepObj) {
            //console.log(stepObj);
            if(stepObj.step == self.maxSteps +1){
                self.preValidate();
            }
        });

        $scope.$on('load', function ($scope) {
            self.showLoader();
        });
        $scope.$on('loadEnd', function ($scope) {
            self.hideLoader();
        });

        $scope.$watch('step', function(newValue, oldValue) {
            $rootScope.$broadcast("newStepAfter", {step:newValue});
        });

        // BROADCASTER
        $scope.next = function(){
            $rootScope.$broadcast("nextStep");
        };

        $scope.prev = function(){
            $rootScope.$broadcast("prevStep");
        };

        $scope.toStep = function(step){
            $rootScope.$broadcast("toStep",{step:step});
        };

        $scope.changed = function(){
            $rootScope.$broadcast("newStep", {step:self.step});
        };

        $scope.setCheckStatus = function(item, status){
            var state = status || false;
            $scope.check[item] = state;
        };

        $scope.getCheckStatus = function(item){
            return $scope.check[item];
        };

        $scope.validate = function(){

            var results = {
                msg : "",
                status : "error"
            };

            // Check empty required fields
            var form  = self.$$childTail.uForm;
            if((form.name.value.length< 5 || form.desc.value.length< 5 || form.attributions.value.length)< 5){
                results.msg = "Il manque des champs requis ( onglet Métadonnees ).";
                return results;
            }

            // Location
            if(self.$$childTail.uMetadata.geocoded == 0){
                results.msg = "Le jeu de données doit être géoréférencé ( onglet Géocodage ).";
                return results;
            }

            // Certification
            var cert = $scope.certification;
            if( !cert.right || !cert.licence){
                results.msg ="Vous devez accepter la licence de publication et certifier avoir le droit de publier les données.";
                return results;
            }

            results.status = "ok";
            return results;
        };

        $scope.preValidate = function(){

            console.log(self);
            var results = {
                msg : "",
                status : "error"
            };

            // Check empty required fields
            var child = self.$$childTail;
            var form  = child.uForm;

            //console.log(form);

            child.setCheckStatus("name", (form.name.value.length > 5 ));
            child.setCheckStatus("description", (form.desc.value.length > 5 ));
            child.setCheckStatus("source", (form.attributions.value.length > 5 ));
            child.setCheckStatus("geo", (child.uMetadata.geocoded == 1 ));

            child.setCheckStatus("categories", (child.cat.hash.length > 0 ));

            child.setCheckStatus("right", $scope.certification.right );
            child.setCheckStatus("licence", $scope.certification.licence );

            console.log($scope.check);
            return $scope.check;
        };


        $scope.publish = function(){
            console.log("Publish in Stepper Scope!!");

            var validation = $scope.validate();
            if(validation.status != "ok"){
                console.log(validation.msg);
                $scope.validationAlert.msg = validation.msg;
                $scope.validationAlert.visibility = 1;
                return false;
            }

            $http.post("./publish",{
                data: self.$$childTail.uData,
                headers: self.$$childTail.uHeaders,
                metadata: self.$$childTail.uMetadata,
                location: self.$$childTail.uLocation,
                form: self.$$childTail.uForm
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
            return true;
        };

        $scope.showLoader = function(){
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
                text: "Analyse",
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

    })
    .controller('UploadCtrl', function($rootScope, $scope, upload) {
        var self = $scope;
        $scope.status=0;
        $scope.msg="";
        $scope.fileFormat="TEST";

        $scope.upload = function() {
            $rootScope.$broadcast("newUpload");
            $rootScope.$broadcast("load");
            upload.submit("ng_upload",$scope.uploadResponse);
        };

        $scope.fileChange = function() {
            console.log($scope.fileFormat);
        };

        $scope.changeFormat = function(format) {
            $scope.$apply(function(){
                self.fileFormat = format;
            })
        };

        $scope.showScope = function() {
            console.log($scope);
        };

        $scope.uploadResponse = function(resp){
            $rootScope.$broadcast("loadEnd");
            if(resp.substring(0,1)!="{"){
                $scope.status = 400;
                $scope.msg = "Une erreur est survenue en traitant le document.";
                return false;
            }

            // NEED CONVERSION FROM text/html to json
            var oData = angular.fromJson(resp);
            console.log('RESPONSE:');
            console.log(oData);

            // SESSION EXPIRED
            if(oData.status == 403){
                $scope.status = 403;
                $scope.msg = "La session est expirée. Veuillez recharger la page.";
                console.log("Session expirée!");
                return false;
            }

            $scope.status = oData.status;
            $scope.msg = oData.msg;

            // Success
            if(oData.status == 200){
                $rootScope.$broadcast('newData', oData);
            }

            return true;
        };

        $scope.viewScope = function(){
            console.log($scope);
            console.log("View PARENT SCOPE");
            console.log($scope.$parent);
            console.log($scope.$parent.$$childTail);
            console.log("View ROOTSCOPE");
            console.log($rootScope);
        };
    })
    .controller('TabPaneCtrl', function($rootScope, $scope) {
        var self = $scope;

        $scope.tabs = [
            {   text:"Téléchargez",
                step:1,
                classActivated:"active",
                classDisabled:"",
                show: true
            },
            {   text:"Aperçu",
                step:2,
                classActivated:"",
                classDisabled:"",
                show:false
            },
            {   text:"Géocodage",
                step:3,
                classActivated:"",
                classDisabled:"",
                show:false
            },
            {   text:"Métadonnées",
                step:4,
                classActivated:"",
                classDisabled:"",
                show:false
            },
            {   text:"Droits & Licence",
                step:5,
                classActivated:"",
                classDisabled:"",
                show: false
            },
            {   text:"Publication",
                step:6,
                classActivated:"",
                classDisabled:"disabled",
                show: false
            }
        ];

        $scope.$on('newData', function () {
            _.each(self.tabs, function(element){
                    element.show = true;
            });
            self.tabs[0].show = false;
        });

        $scope.$on('clearData', function () {
            _.each(self.tabs, function(element){
                element.show = false;
            });
            self.tabs[0].show = true;
        });

        $scope.$on('newStep', function ($scope, params) {
            _.each(self.tabs, function(element){
                if(element.step == params.step){
                    element.classActivated = "active";
                }else{
                    element.classActivated = "";
                }
            });
        });
    })
    // ## GridCtrl
    // ### Injections/Services: $scope, $rootScope, $http
    .controller('GridCtrl', function($scope,$rootScope, $http) {
        var self = $scope;
        $scope.uData = [];
        $scope.uLocation = [];
        $scope.uHeaders = [];
        $scope.uMetadata = [];
        $scope.uForm = [];
        $scope.dataExtract = [];
        $scope.geoConsole = "Appuyez sur le bouton geocodage pour lancer l'operation.";
        $scope.geoLog = "------------------------------\nConsole de geocodage\n------------------------------";
        $scope.geocodingPogress = {width:'0%'};
        $scope.geocodingErrors = 0;
        $scope.geocodingBtn = {isDisabled:false};

        $scope.cat = {
            "hash":[],
            "options" : []
        };

        $http.get("./categories").
            success(function(data) {
                $scope.cat.options = data.results;
            }).
            error(function(data, status) {
                console.log(status);
                console.log(data);
            });

        // ### newUpload *Listener*
        $scope.$on('newUpload', function () {
            //console.log("newUpload LISTENER >> GridCtrl");
            self.uData = [];
            self.uHeaders = [];
            self.uMetadata = [];
            self.dataExtract = [];

            self.uLocation = [];
            self.uGeoJson  = [];
        });

        // ### newData *Listener*
        $scope.$on('newData', function ($scope, oData) {

            //console.log(oData);
            self.uData = oData.data;
            self.uHeaders = oData.headers;
            self.uMetadata = oData.metadata;
            self.uForm = oData.metadata['form'];
            self.uForm.label.value = self.uHeaders[0].title;

            self.uGeoJson = oData.geoJson;

            //Extract
            for(var i=0;i<3;i++){
                self.dataExtract[i] = self.uData[i];
            }

            if(self.uMetadata.geocoded == 0){
                self.geocoder = new google.maps.Geocoder();
            }

            if(self.uMetadata.geocoded == 1){
                // Build uLocation
                var latFieldIndex = 0;
                var lonFieldIndex = 0;
                var metaLatField = self.uMetadata.latField;
                var metaLonField = self.uMetadata.lonField;
                _.each(self.uHeaders, function(element, index){
                    if(element.title == metaLatField){
                        latFieldIndex = index;
                    }
                    if(element.title == metaLonField){
                        lonFieldIndex = index;
                    }
                });

                _.each(self.uGeoJson, function(element){
                    var geometry = {
                        type:"Point",
                        coordinates: [element[self.uMetadata.lonField],element[self.uMetadata.latField]]
                    };
                    element['geometry'] = geometry;
                });

                _.each(self.uData, function(element){
                    var geometry = {
                        type:"Point",
                        coordinates: [element[lonFieldIndex],element[latFieldIndex]]
                    };
                    element['geometry'] = geometry;

                    self.uLocation.push({
                        lat:element[latFieldIndex],
                        lon:element[lonFieldIndex]//,
                        //label: "label"
                    });
                });
                $rootScope.$broadcast("setMarkers", self.uLocation);
            }

            self.safeApply();
        });

        $scope.$on('newStepAfter', function ($scope, stepper) {
            if(stepper.step === 3 && self.uMetadata.geocoded === 1){
                setTimeout(function(){
                    self.bounds();
                },800 );
            }
        });

        $scope.$on('clearData', function ($scope) {
            self.uData = [];
            self.uHeaders = [];
            self.uMetadata = [];
            self.dataExtract = [];
            self.uLocation = [];
            self.uGeoJson = [];

            self.geoConsole = "";
            self.geoLog = "";
            self.geoIndex = 0;
        });

        $scope.$on('geocoded', function ($scope, errorsCount) {
            console.log("==== GEOCODED ====");
            console.log(self.uData);
            console.log(self.uHeaders);
            console.log(self.uLocation);

            if(errorsCount == 0){
                self.uMetadata['geocoded'] = 1;
                self.$broadcast('setMarkers', self.uLocation);
                self.bounds();
            }

            //New Extract
            self.dataExtract = [];
            for(var i=0;i<3;i++){
                self.dataExtract[i] = self.uData[i];
            }

            //self.$apply();
            self.safeApply();
        });

        $scope.$on('setMarkers', function ($scope, Places) {
            if (self.map.hasLayer(self.markers)) {
                self.map.removeLayer(self.markers);
                self.markers = null;
            }

            var headers = [];

            _.each(self.uHeaders, function(element){
                headers.push(element.title);
            });


            self.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});
            //self.markers.on('click', function (a) {
                //console.log(a);
                //console.log(self.uData[a.layer.options.index]);
                //$rootScope.$broadcast('setQueryById', {options:a.layer.options});
            //});

            /*self.markers.on('clusterclick', function (a) {
                console.log(a);
                _.each(a.layer.getAllChildMarkers(), function (element, index, list) {
                    console.log(element.options.title );
                });
            });*/
          var t_bounds = [];
            _.each(Places, function(element, index){
                var attributes = [];
                var headIndex = 0;
                _.every(headers, function(head){
                    attributes.push(head + ": "+ self.uData[index][headIndex] +"<br>");
                    if(headIndex > 3 ){
                        attributes.push("...<br>");
                        return false;
                    }
                    headIndex ++;
                    return true;
                });

                var title = attributes.join('');

                t_bounds.push(new L.LatLng(element.lat, element.lon));
                var marker = new L.Marker(new L.LatLng(element.lat, element.lon), { title:title,index:index });
                marker.bindPopup(title);
                self.markers.addLayer(marker);
            });

            self.map.addLayer(self.markers);
            if (t_bounds.length == 1) {
                //console.log("Set View");
                self.map.setView(t_bounds[0], 15);
            }

            self.t_bounds = t_bounds;
        });

        $scope.bounds = function(){
            var bounds = new L.LatLngBounds(self.t_bounds);
            self.map.fitBounds(bounds);
        };

        $scope.geocode = function () {

            self.disable();
            var idx = self.findHeaderIndex( self.uMetadata.locField );
            var pos = self.geoIndex || 0;
            var l = self.uData.length -1;
            var timer = 800;

            (function () {
                var adr = self.uData[pos][idx];
                var targetRow = pos;

                self.geocoder.geocode( { 'address': adr}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var lon = results[0].geometry.location.lng();
                        var lat = results[0].geometry.location.lat();
                        var location_type = results[0].geometry.location_type;
                        var address_components = results[0].address_components;
                        var formatted_address = results[0].formatted_address;
                        var postal_code ="";
                        var city = "";

                        _.each(address_components, function(item, index){

                            if( item.types[0] === "postal_code"){
                                postal_code = item.long_name;
                            }

                            if( item.types[0] === "administrative_area_level_1"){
                                city = item.long_name;
                            }
                        });

                        var location = {
                                "lon":lon,
                                "lat":lat,
                                "location_type" : location_type,
                                "formatted_address" : formatted_address,
                                "postal_code" : postal_code,
                                "city" : city,
                                "service" : "Google"
                            };
                        self.uLocation[targetRow]= location;


                       // self.uData[targetRow].splice(self.uData.length,0,lon,lat);
                        self.geoConsole = "ID:"+targetRow+" ["+adr+"] : ("+lon+","+lat+")";
                        self.geoLog += "\n" + self.geoConsole;
                        timer = 800;
                        self.geoIndex = pos;
                        pos ++;

                    } else
                    {
                        if(status == "OVER_QUERY_LIMIT"){
                            self.geoConsole = "(PAUSE 3 sec.) > Message du service Web: "+ status+". => ID:"+pos+". Nous allons reessayer dans 3 secondes";
                            self.geoLog += "\n" + self.geoConsole;
                            timer = 3000;
                        }else{
                            self.geoConsole = "=> ID:"+pos+" ("+adr+") Erreur: "+ status;
                            self.geoLog += "\n" + self.geoConsole;
                            self.uData[targetRow].push('');
                            self.uData[targetRow].push('');
                            self.geocodingErrors ++;
                            pos ++;
                        }
                    }
                });

                self.geocodingPogress.width = Math.round((pos/(l-1))*100) + "%";
                self.safeApply();

                if (pos < l) {
                    self.geoTimeout = setTimeout(arguments.callee, timer);
                } else {
                    self.$broadcast('geocoded', self.geocodingErrors);
                }
            })();
        };

        $scope.clearGeo = function(){
            clearTimeout(self.geoTimeout);
            self.geocodingBtn.isDisabled = false;
        };

        $scope.findHeaderIndex = function(needle){
            var idx = '';
            _.each($scope.uHeaders, function(element, index){
                if(element.title == needle){
                    idx = index;
                    return false;
                }
            });
            return idx;
        };

        $scope.disable = function()
        {
            self.geocodingBtn.isDisabled = true;
        };

        $scope.removeDataset = function(){
            $rootScope.$broadcast("load");
            $http.delete("./remove/"+ self.uMetadata.fileUri).
                success(function(data, status) {
                    console.log(status);
                    console.log(data);

                    $scope.uData = [];
                    $rootScope.$broadcast("clearData");
                    $rootScope.$broadcast("loadEnd");


                }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    $rootScope.$broadcast("loadEnd");
                });
        };

        $scope.capitaliseFirstLetter = function(string)
        {
            return string.charAt(0).toUpperCase() + string.slice(1);
        };

        $scope.viewScope = function(){
            var self = $scope;
            console.log($scope);
            //console.log(self.uLocation);
        };
    });