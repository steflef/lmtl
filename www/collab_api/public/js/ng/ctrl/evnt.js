function EvntCtrl($rootScope, $scope, $filter, $http, $location) {

    //bootstap with embeded data
    $scope.evnts = evnts;

    $scope.query = '';
    $scope.queryLength = 0;
    $scope.orderList = 'dt';
    $scope.reverseCheck = 'true';
    $scope.evntsLength;

    $scope.alert = {
        display:false,
        lastQuery:''
    };

    $scope.autoRefresh = {
        checked:false,
        GATLAS_refresh:null
    };

    var _evnts = $scope.evnts;
    var _scope = $scope;

    $scope.unstar = function(id, index){

        $scope.evnts.splice(index, 1);
        $rootScope.$broadcast('setMarkers', {evnts:$scope.evnts,query:''});
        $rootScope.$broadcast('serverUnstar', {"id":id});
    }

    $scope.$on('setQuery', function ($scope, event) {

        _scope.query = event.desc;
        _scope.queryLength = (_scope.query.length > 0 ) ? 1 : 0;
    });

    $scope.$on('setQueryById', function ($scope, event) {

        var target = _.find(_scope.evnts, function (obj) {
            return obj.id == event.options.title;
        });
        if(target !== undefined){
            _scope.$apply(function () {
                _scope.query = target.id;
            });
        }
    });

    $scope.$on('noData', function ($scope, event) {

        _scope.alert.display = event.showAlert;
        _scope.alert.lastQuery = event.lastQuery;
    });

    $scope.hideAlert = function(){

        _scope.alert.display = false;
    };

    $scope.$on('newDataset', function ($scope, event) {
        //adding isHidden
        _.map( event.dataset.data, function(obj){return obj.isHidden = false;});
        _scope.evnts = event.dataset.data;
        _scope.alert.lastQuery = event.lastQuery;
       // _scope.$broadcast('setMarkers', {});
        $rootScope.$broadcast('setMarkers', {evnts:_scope.evnts,query:_scope.query});
    });

    $scope.$watch('query', function (newValue, oldValue) {

        $rootScope.$broadcast('setMarkers', {evnts:_scope.evnts,query:_scope.query});
        _scope.queryLength = (_scope.query.length > 0 ) ? 1 : 0;
    });

    $scope.$watch('autoRefresh.checked', function (newValue) {

        if (newValue === true) {
            _scope.autoRefresh.GATLAS_refresh = setTimeout(function () {
                window.location.href = BASE_URL;
            }, 180000);
        }else{
            clearTimeout(_scope.autoRefresh.GATLAS_refresh);
            _scope.autoRefresh.GATLAS_refresh = null;
        }
    });

    //$rootScope.$broadcast('setMarkers', {evnts:_scope.evnts,query:_scope.query});
}

EvntCtrl.$inject = ['$rootScope', '$scope', '$filter', '$http', '$location'];