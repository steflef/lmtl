function ForageCtrl($rootScope,$scope,$filter,$http,$location){

    $scope.forage = {};
    var _f = $scope.forage;
    var _scope= $scope;
    var _location = $location;
    var _root = $rootScope;

    var to = new Date();
    var from = new Date(to.getTime() - 1000 * 60 * 60 * 24 * 14);
    var from = new Date(to.getTime() - 1000 * 60 * 60 * 24 * 2920); // way back

    _f.filters = [
        {
            name:"par-date",
            value:''
        },
        {
            name:"par-jour",
            value:''
        },
        {
            name:"par-heure",
            value:''
        },
        {
            name:"par-priorite",
            value:''
        },
        {
            name:"par-code",
            value:''
        },
        {
            name:"par-geo",
            value:''
        }];

    _f.autoSubmit = {
        checked:true,
        label:'Mise à jour automatique'
    };

    _f.submitQuery = {
        buttonState:'disabled',
        label:'Mise à jour'
    };

    _f.liveRequest = {
        active:0,
        lastQuery:$location.path(),
        requestTimestamp:'',
        total:0,
        duration:0
    };

    _f.modal = {
        name:''
    };

    _f.starButton = {
        isDisabled:false,
        isStared:false,
        btnClass:'default',
        btnText:'Enregistrer le forage actif',
        btnTextVisibility:'',
        starClass:'default'
    };

    _f.date = {
        checked:true,
        isDisabled:true,
        to:to,
        from:from,
        label:'Par Date',
        rangeText:'30 Août, 2012 - 13 Septembre, 2012'
    };
    _f.days = {
        checked:false,
        isDisabled:false,
        label:'Par Jour',
        val:[
            {shortText:'Lun',
                checked:true
            },
            {shortText:'Mar',
                checked:true
            },
            {shortText:'Mer',
                checked:true
            },
            {shortText:'Jeu',
                checked:true
            },
            {shortText:'Ven',
                checked:true
            },
            {shortText:'Sam',
                checked:false
            },
            {shortText:'Dim',
                checked:false
            }
        ]
    };
    _f.hours = {
        checked:false,
        isDisabled:false,
        label:'Par Heure',
        hoursData:[],
        from:0,
        to:24,
        val:'0:00,24:00'
    };

    _f.priority = {
        checked:false,
        isDisabled:false,
        label:'Par Priorité',
        data:[
            {label:'P1',
                value:1,
                checked:false},
            {label:'P2',
                value:2,
                checked:false},
            {label:'P3',
                value:3,
                checked:false},
            {label:'P4',
                value:4,
                checked:false}
        ],
        hash:[]
    };

    _f.rao = {
        checked:false,
        isDisabled:false,
        label:{
            main:'Par Code/Description RAO',
            sub:'Choix multiples avec regroupements'
        },
        url:'par-code',
        data:[],
        hash:[]
    };

    _f.geo = {
        checked:false,
        isDisabled:true,
        label:{
            main:'Par Vecteurs Géographiques',
            sub:"Activer/Désactiver via l'interface graphique, partie supérieure droite de la carte."
        },
        url:'par-geo',
        feature:{},
        data:[],
        hash:[]
    };


    /*
     * Parse url string
     *
     */
    _f.parsePath = function(path){
        var tokens = path.split("/");
        _.each( _f.filters, function(element, index){
            _.each( tokens, function(element1, index1){
                if(element.name === element1){
                    element.value = tokens[index1+1];
                }
            });
        });
        // Removing unused filters
        _f.filters = _.reject(_f.filters, function(item){ return item.value === "";});
        //console.log(_f.filters);
        return _f.filters;
    };

    _f.buildFeature = function(element){
        var params = element.value.split(":");
        var feature = {};
        var latlngs = [];

        //console.log(params[1].split("_"));
        if (params[0] === "Polygon") {

            _.each(params[1].split("_"), function (item) {

                var point = item.split(",");
                //latlngs.push( {lng:point[0],lat:point[1]});
                latlngs.push(new L.LatLng(point[1], point[0]));
            });

            feature = {
                "type":"Feature",
                "geometry":{
                    "type":params[0],
                    "coordinates":latlngs
                },
                "properties":{
                }
            };
        }
        if (params[0] === "Point") {

                var point = params[2].split(",");
                var latlng = new L.LatLng(point[1], point[0]);

            feature = {
                "type":"Feature",
                "geometry":{
                    "type":params[0],
                    "coordinates":latlng
                },
                "properties":{
                    "radius":params[1]
                }
            };
        }

        return feature;
    };

    /*
     * Build Model from the parsed url
     *
     */
    _f.updateModelFromPath = function (newModel) {
        _.each(newModel, function (element, index) {
            switch (element.name) {
                case 'par-date':
                    var dates = element.value.split(",");
                    var from = new Date(dates[0].split("-").join(","));
                    var to = new Date(dates[1].split("-").join(","));
                    _f.date.from = from;
                    _f.date.to = to;
                    break;
                case 'par-jour':
                    _f.days.checked = true;
                    var days = element.value.split(",");
                    _.each(_f.days.val, function (element) {
                        element.checked = false;
                        if (_.indexOf(days, element.shortText.toLowerCase()) !== -1) {
                            element.checked = true;
                        }
                    });
                    break;
                case 'par-heure':
                    _f.hours.checked = true;
                    var days = element.value.split(",");
                    _f.hours.val = element.value;
                    var from = days[0].split(":");
                    _f.hours.from = new Date(null, null, null, from[0], from[1]).getHours();
                    var to = days[1].split(":");
                    _f.hours.to = new Date(null, null, null, to[0], to[1]).getHours();
                    break;
                case 'par-priorite':
                    _f.priority.checked = true;
                    var priorities = element.value.split(",");
                    _.each(_f.priority.data, function (pri) {
                        pri.checked = false;
                        if (_.indexOf(priorities, pri.value.toString()) !== -1) {
                            pri.checked = true;
                        }
                    });
                    break;
                case 'par-code':
                    _f.rao.checked = true;
                    _f.rao.hash = element.value.split(",");
                    break;
                case 'par-geo':
                    _f.geo.checked = true;
                    //_f.geo.feature.geometry = ['test'];
                    _f.geo.feature = _f.buildFeature(element);
                    break;
                default:
            }
        });
    }

    // Inject data in hours model
    for(var i=0;i<25;i++){
        _f.hours.hoursData.push(i);
    }

    // Force a Model rebuild if a Path is detected
    var _path = $location.path();
    if(_path !== ''){
        _f.updateModelFromPath(_f.parsePath(_path));
    }
    /**
     * Asynchronous call to RAO API to get list of calls matching selected filters
     * Only one request at time (validated via _f.liveRequest.active flag)
     * If Status === 200 > Emit a app wide broadcast
     * If Status !== 200 (404) > Error or no match
     */
    _f.fetchCalls = function () {
        var lq = _f.liveRequest;
        var _oldQuery = lq.lastQuery;

        if (lq.active === 0) {
            var query = _f.buildQuery();
            lq.active = 1;
            lq.lastQuery = query;
            lq.requestTimestamp = new Date();

            $http.get(BASE_URL+'/api/rao/appels/' + query + '/format/json')
                .then(
                function (result) {
                    if (result.status === 200) {

                        $rootScope.$broadcast('noData',{showAlert:false});
                        $rootScope.$broadcast('newDataset', {dataset:result.data,lastQuery:query});
                        lq.active = 0;
                        lq.total = result.data.total;
                        lq.duration = result.data.duration;

                        $rootScope.$broadcast('heat', result.data.data);
                    }
                },
                function (reason) {
                    $rootScope.$broadcast('noData',{showAlert:true,headers:reason.headers(),lastQuery:_oldQuery});
                    lq.lastQuery = _oldQuery;
                    lq.active = 0;
                }
            );
        }
    }

    _f.buildQuery = function () {
        var mainQuery,parDate,parJourVals,parJour,parHeure,parPriorite,parRao,parGeo;
        mainQuery = '';

        // DATE
        parDate = 'par-date/'+ $scope.forage.date.from +','+ $scope.forage.date.to;
        mainQuery += ($scope.forage.date.checked === true)?parDate:'';

        // DAYS
        parJourVals = [];
        _.each($scope.forage.days.val, function(item){ if(item.checked === true) parJourVals.push(item.shortText); });
        parJour = 'par-jour/'+parJourVals.join(",").toLowerCase();
        mainQuery += ($scope.forage.days.checked === true)?'/'+parJour:'';

        // HOURS
        parHeure = $scope.forage.hours.val;
        mainQuery += ($scope.forage.hours.checked === true)?'/par-heure/'+parHeure:'';

        // PRIORITY
        parPriorite = [];
        _.each($scope.forage.priority.data, function(item){ if(item.checked === true) parPriorite.push(item.value); });
        mainQuery += ($scope.forage.priority.checked === true)?'/par-priorite/'+parPriorite.join(",").toLowerCase():'';

        // RAO
        parRao = $scope.forage.rao.hash;
        mainQuery += ($scope.forage.rao.checked === true)?'/par-code/'+parRao.join(","):'';

        // GEO
        //console.log($scope.forage.geo);
        if ($scope.forage.geo.feature.geometry !== undefined) {
            var feature = $scope.forage.geo.feature;
            var fType = feature.geometry.type;
            if(fType === 'Point'){
                parGeo = fType +':' + feature.properties.radius + ':' + feature.geometry.coordinates.lng +','+feature.geometry.coordinates.lat;
            }else{
                var latlngs = [],
                    parGeo = '';
                _.each(feature.geometry.coordinates, function (item) {
                    latlngs.push(item.lng + "," + item.lat);
                });
                parGeo = fType +':' + latlngs.join("_");
            }

            mainQuery +=  '/par-geo/' + parGeo;
        }

        $location.path(mainQuery);
        return mainQuery;
    }

    /**
    *  Click button to send main query
    *  ng-click Activated
    *
    *  @return void
    */
    $scope.forceRefresh = function(){

        _f.fetchCalls();
    }

    this.saveForage = function(){

        //console.log("SAVE!!");
//        console.log($scope.forage.modal.name);
//        console.log($scope.forage.liveRequest.lastQuery);

        $scope.$apply(function(){
            $scope.forage.starButton.isStared = true;
        });

        $http.put(
            BASE_URL+'api/rao/forage/format/json',
            {
                name:$scope.forage.modal.name,
                uri:$scope.forage.liveRequest.lastQuery,
                total:$scope.forage.liveRequest.total,
                duration:$scope.forage.liveRequest.duration
            }
        )
            .then(
            function (result) {
                if (result.status === 200) {

                    console.log(result);
                }
            },
            function (reason) {

                console.log(reason);
            }
        );
    }

//    $scope.hideModal = function(){
//
//        $scope.forage.modal.hide = true;
//    }
//
//    $scope.showModal = function(){
//
//        $scope.forage.modal.hide = false;
//    }
    // SUBMIT BUTTON STAT WATCH
    $scope.$watch('forage.autoSubmit.checked', function(newValue){

        $scope.forage.submitQuery.buttonState = (newValue === false)? 'enabled' : 'disabled';
    });

    // DATE WATCH
    $scope.$watch('forage.date.rangeText', function(newValue){

        if($scope.forage.submitQuery.buttonState === 'disabled'){
            $scope.$broadcast('newQuery',$scope.forage.date);
        }
    });

    // DAY WATCH
    $scope.$watch('forage.days.val', function (newValue, oldValue, scope) {

        if($scope.forage.submitQuery.buttonState === 'disabled' && $scope.forage.days.checked === true){
            $scope.$broadcast('newQuery',{});
        }
    },true);

    // HOURS WATCH
    $scope.$watch('forage.hours.val', function (newValue, oldValue, scope) {

        if($scope.forage.submitQuery.buttonState === 'disabled' && $scope.forage.hours.checked === true){
            $scope.$broadcast('newQuery',{});
        }
    });

    // PRIORITY WATCH
    $scope.$watch('forage.priority.data', function (newValue, oldValue, scope) {

        var hash = _.map( $scope.forage.priority.data, function (item) {
            return (item.checked === true) ? 1 : 0;
        });
        var sHash = hash.join(',');
        if($scope.forage.submitQuery.buttonState === 'disabled' && $scope.forage.priority.checked === true){
            $scope.$broadcast('newQuery',{});
        }
    },true);

    // RAO WATCH
    $scope.$watch('forage.rao.hash', function () {

        if($scope.forage.submitQuery.buttonState === 'disabled' && $scope.forage.rao.checked === true){
            $scope.$broadcast('newQuery',{});
        }
    },true);

    // GEO WATCH
    $scope.$watch('forage.geo.feature', function(newValue){

        $scope.forage.geo.checked = (newValue.geometry === undefined)? false : true;
    },true);

    $scope.$watch('forage.geo.hash', function(newValue){

        $rootScope.$broadcast('mapDraw',$scope.forage.geo.feature);
    },true);

    //STAR BUTTON
    $scope.$watch('forage.starButton.isStared', function(newValue){

        if(newValue){
            $scope.forage.starButton.btnClass ='info';
            $scope.forage.starButton.starClass ='white';
            $scope.forage.starButton.btnTextVisibility ='noDisplay'
        }else{
            $scope.forage.starButton.btnClass ='default';
            $scope.forage.starButton.starClass ='default';
            $scope.forage.starButton.btnTextVisibility =''
        }
    });

    // setGeoFilter LISTENER
    $scope.$on('setGeoFilter', function ($mapctrlScope, feature) {

        $scope.$apply(function(){
            _scope.forage.geo.feature = feature;
        });
        $scope.$broadcast('newQuery',{});
    });

    /**
     * Populate rao Model with remote data
     *
     * @return $scope.forage.rao.data
     */
    $scope.fetchRao = function(){
        //console.log(BASE_URL);
        $http.get(BASE_URL+'raoevnts/get_list/'+$scope.forage.rao.url).then(function(result){
            _.each(result.data.list, function(val,key){
                val.group = "RAO 911";
            });
            $scope.forage.rao.data = result.data.list;
            return $scope.forage.rao.data;
        });
    };

    $scope.$on('newQuery', function($scope,obj){
        _f.starButton.isStared = false;
        _f.fetchCalls();
    });

    $scope.hoursUp = function(){

        $scope.forage.hours.val = $scope.forage.hours.from +':00,' +  $scope.forage.hours.to+':00';
    }

    $scope.fetchRao();
}

ForageCtrl.$inject = ['$rootScope','$scope','$filter','$http','$location'];