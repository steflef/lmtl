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
        var linker = function(scope) {
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
            scope.$watch('cat.options', function(){
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
    .controller('StepperCtrl', function($rootScope, $scope, $http) {

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
            licence: false,
            text:"odbl"
        };
        $scope.validationAlert = {
            visibility:0,
            msg:""
        };

        // Listeners
        $scope.$on('nextStep', function () {
            if(self.step + 1 <= self.maxSteps){
                self.step ++;
                self.changed();
            }
        });

        $scope.$on('prevStep', function () {
            if(self.step - 1 > 0){
                self.step --;
                self.changed();
            }
        });

        $scope.$on('toStep', function ($scope, param) {
            self.step = param.step;
            self.changed();
        });

        $scope.$on('clearData', function () {
            self.step = 1;
            self.changed();
        });

        $scope.$on('newData', function () {
            self.toStep(2);
        });

        $scope.$on('newStep', function ($scope, stepObj) {
            //console.log(stepObj);
            if(stepObj.step == self.maxSteps +1){
                self.preValidate();
            }
        });

        $scope.$on('load', function () {
            var text = arguments[1] || "";
            self.showLoader(text);
        });
        $scope.$on('loadEnd', function () {
            self.hideLoader();
        });
        $scope.$on('showMsg', function () {
            var msg = arguments[1] || {title:"",text:""};
            self.showMsg(msg);

            setTimeout(function(){
                    self.hideMsg();
                },5000
            );
        });

        $scope.$on('newPublication', function () {

            var oMsg = {
                title: "Publié avec succès!",
                text: "Redirection vers le Tableau de bord dans 2 sec."
            };
            self.showMsg(oMsg);

            setTimeout(function(){
                    window.location.href = "./";
                },3000
            );

        });

        $scope.$watch('step', function(newValue) {
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

        $scope.getStep = function(){
            return $scope.step;
        };

        $scope.changed = function(){
            $rootScope.$broadcast("newStep", {step:self.step});
        };

        $scope.setCheckStatus = function(item, status){
            $scope.check[item] = status || false;
        };

        $scope.getCheckStatus = function(item){
            return $scope.check[item];
        };

        $scope.validate = function(){

            var results = {
                data : {},
                msg : "",
                status : "error"
            };

            // Check empty required fields
            var form  = self.$$childTail.uMetadata.form;
            if((form.name.value.length< 5 || form.desc.value.length< 5 || form.attributions.value.length)< 5){
                results.msg = "Il manque des champs requis ( onglet Métadonnees ).";
                return results;
            }
            results.data.nom = form.name.value;
            results.data.description = form.desc.value;
            results.data.source = form.attributions.value

            results.data.categories = self.$$childTail.cat.hash.join(",");

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
            results.data.licence = cert.text;

            results.status = "ok";
            return results;
        };

        $scope.preValidate = function(){

            var child = self.$$childTail;

            child.setCheckStatus("name", (child.uMetadata.form.name.value.length > 5 ));
            child.setCheckStatus("description", (child.uMetadata.form.desc.value.length > 5 ));
            child.setCheckStatus("source", (child.uMetadata.form.attributions.value.length > 5 ));
            child.setCheckStatus("geo", (child.uMetadata.geocoded == 1 ));

            child.setCheckStatus("categories", (child.cat.hash.length > 0 ));

            child.setCheckStatus("right", $scope.certification.right );
            child.setCheckStatus("licence", $scope.certification.licence );

            console.log($scope.check);
            return $scope.check;
        };

        $scope.publish = function(){

            var validation = $scope.validate();
            if(validation.status != "ok"){
                $scope.validationAlert.msg = validation.msg;
                $scope.validationAlert.visibility = 1;
                return false;
            }

            var metadata = validation.data;
            metadata.uri = self.$$childTail.uMetadata.fileUri;
            metadata.etiquette = self.$$childTail.uMetadata.form.label.value;
            metadata.att_name = self.$$childTail.uMetadata.fieldName;
            metadata.att_description = self.$$childTail.uMetadata.fieldDescription;
            metadata.c_categorie = self.$$childTail.uMetadata.form.field_category.value;

            $rootScope.$broadcast("load", "Publication");
            $scope.safeApply();

            $http.post("./publish",{
                geojson: self.$$childTail.uData,
                metadata: metadata,
                properties: self.$$childTail.uMetadata.properties
            }).
            success(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.status = status;
                    //$scope.data = data;
                    $rootScope.$broadcast("loadEnd");
                    $rootScope.$broadcast("newPublication");
            }).
                error(function(data, status) {
                    console.log(status);
                    console.log(data);
                    //$scope.data = data || "Request failed";
                    $scope.status = status;
                    $rootScope.$broadcast("loadEnd");

                    var oMsg = {
                        title: "Erreur lors de la publication",
                        text: "Code: "+status+". Consultez la consolle pour plus d'infos."
                    };
                    $rootScope.$broadcast("showMsg", oMsg);
                });
            return true;
        };

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

    })
    .controller('UploadCtrl', function($rootScope, $scope, upload) {
        var self = $scope;
        $scope.status=0;
        $scope.msg="";
        $scope.fileFormat="TEST";

        $scope.upload = function() {
            $rootScope.$broadcast("newUpload");
            $rootScope.$broadcast("load","Chargement");
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
            //console.log(resp);
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
        $scope.uMetadata = [];
        $scope.dataExtract = [];
        $scope.geoConsole = "Appuyez sur le bouton geocodage pour lancer l'operation.";
        $scope.geoLog = "------------------------------\nConsole de geocodage\n------------------------------";
        $scope.geocodingProgress = {width:'0%'};
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
            self.uMetadata = [];
            self.dataExtract = [];
        });

        // ### newData *Listener*
        $scope.$on('newData', function ($scope, oData) {

            self.uData = oData.geojson;
            self.uMetadata = oData.metadata;
            // Champ Label Promise >> blank otherwise
            var firstPropertieTitle = self.uMetadata.properties[0].title;
            self.uMetadata.form.label.value = firstPropertieTitle;
            self.uMetadata.fieldName = firstPropertieTitle;
            self.uMetadata.fieldDescription = firstPropertieTitle;

            //Extract
            for(var i=0;i<3;i++){
                self.dataExtract[i] = self.uData.features[i];
            }

            if(self.uMetadata.geocoded == 0){

                self.geocoder = new google.maps.Geocoder();
            }else{
                var lonField = self.uMetadata.lonField;
                var latField = self.uMetadata.latField;
                _.each(self.uData.features, function(item){
                    var location = {
                        "lon":item.properties[lonField],
                        "lat":item.properties[latField],
                        "location_type" : "",
                        "formatted_address" : (item.properties.address || item.properties["adresse"] || item.properties["Adresse"] || ""),
                        "postal_code" : (item.properties.postal_code || item.properties["code_postal"] || item.properties["C.P."] || ""),
                        "city" : (item.properties.city || item.properties["ville"] || item.properties["Ville"] || ""),
                        "service" : ""
                    };

                    item.geometry.coordinates = [location.lon,location.lat];
                    item._geo = location;

                });

                $rootScope.$broadcast("setMarkers", self.uData.features);
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

        $scope.$on('clearData', function () {
            self.uData = [];
            self.uMetadata = [];
            self.dataExtract = [];

            self.geoConsole = "";
            self.geoLog = "";
            self.geoIndex = 0;
            self.geocodingProgress.width = "0%";
            self.geocodingBtn.isDisabled = false;
        });

        $scope.$on('geocoded', function ($scope, errorsCount) {
            console.log("==== GEOCODED : "+errorsCount +" ====");
            if(errorsCount == 0){
                self.uMetadata['geocoded'] = 1;
                self.geocodingBtn.isDisabled = false;

                self.safeApply();

                self.$broadcast('setMarkers', self.uData.features);
                //setTimeout( self.bounds(),800 );
            }
        });

        $scope.$on('setMarkers', function ($scope, Places) {
            //console.log("==== setMarkers ====");
            //console.log(Places);

            if (self.map.hasLayer(self.markers)) {
                self.map.removeLayer(self.markers);
                self.markers = null;
            }

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
            var placesIndex = 0;
            _.each(Places, function(element, index){
                var attributes = [];
                var propertiesIndex = 0;
                var pairs = _.pairs(element.properties);
                _.every(pairs, function(p){
                    attributes.push(p[0] + ": "+ p[1] +"<br>");
                    if(propertiesIndex > 3 ){
                        attributes.push("...<br>");
                        return false;
                    }
                    propertiesIndex ++;
                    return true;
                });

                var LL = new L.LatLng(element.geometry.coordinates[1], element.geometry.coordinates[0]);
                t_bounds.push(LL);
                var marker = new L.Marker(LL, { title:attributes.join(''),index:index });
                marker.bindPopup(attributes.join(''));
                self.markers.addLayer(marker);
                placesIndex ++;
            });

            self.map.addLayer(self.markers);
            if (t_bounds.length == 1) {
                self.map.setView(t_bounds[0], 15);
            }

            self.t_bounds = t_bounds;
            //console.log(self.$parent);
            if( self.$parent.getStep() == 3){
                setTimeout(self.bounds(),800);
            }

        });

        $scope.bounds = function(){
            var bounds = new L.LatLngBounds(self.t_bounds);
            self.map.fitBounds(bounds);
        };

        $scope.geocode = function () {

            self.disable();
            var locationField = self.uMetadata.locField;
            var pos = self.geoIndex || 0;
            var l = self.uData.features.length;
            var timer = 800;
            var responseCount = 0;

            (function () {
                var adr = self.uData.features[pos].properties[locationField];
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

                        _.each(address_components, function(item){

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

                        self.uData.features[targetRow].geometry.coordinates = [lon,lat];
                        self.uData.features[targetRow]._geo = location;

                        self.geoConsole = "ID:"+targetRow+" ["+adr+"] : ("+lon+","+lat+")";
                        self.geoLog += "\n" + self.geoConsole;
                        self.safeApply();
                        timer = 800;
                        self.geoIndex = pos;
                        responseCount ++;
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
                            //self.uData[targetRow].push('');
                            //self.uData[targetRow].push('');
                            self.geocodingErrors ++;
                            responseCount ++;
                            pos ++;
                        }
                    }
                });

                self.geocodingProgress.width = Math.round((responseCount/(l-1))*100) + "%";
                self.safeApply();

                if (pos < l-1) {
                   // console.log("Responses:" + responseCount);
                    self.geoTimeout = setTimeout(arguments.callee, timer);
                } else {
                    //console.log("END");
                    //self.safeApply();
                    //self.$broadcast('geocoded', self.geocodingErrors);
                    var interval = setInterval(
                        function(){
                            console.log("++ Responses:" + responseCount);
                            self.geocodingProgress.width = Math.round((responseCount/(l-1))*100) + "%";
                            self.safeApply();
                            if(responseCount == l){
                                self.$broadcast('geocoded', self.geocodingErrors);
                                responseCount = 0;
                                clearInterval(interval);
                            }

                        },800);
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
            //var self = $scope;
            console.log($scope);
            //console.log(self.uLocation);
        };
    });