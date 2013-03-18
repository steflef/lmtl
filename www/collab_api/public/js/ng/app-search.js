var match = 0;
angular.module('appSearch', ['ngSanitize'])

    .config(function ($routeProvider, $locationProvider) {
        var match = 0;
//        $routeProvider.
//            when('/details/:idCall', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/details.html", controller:searchCtrl}).
//            //when('/home', {controller:HomeCtrl}).
//            otherwise({redirectTo:'/home'});
    })
    .directive('toolbar', function () {
        var linker = function(scope, element, attrs) {
            scope.$broadcast('newMenu', {id:'search'});
        };

        return{
            restrict:'E',
            replace: true,
            templateUrl:'./public/js/ng/tmpl/toolbar_partial.html',
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
    })
    .filter('match', function () {

        return function (text, filter) {
            if (filter === undefined) {
                return text;
            } else {
                return text.replace(new RegExp(filter, 'gi'), '<span class="label label-success match">$&</span>');

            }
        }
    });

function SearchCtrl($rootScope, $scope, $filter, $http, $location) {
    $scope.evnts = search;

    //$scope.match = 0;

    $scope.query = '';
    $scope.queryLength = 0;
    $scope.orderList = 'dt';
    $scope.reverseCheck = 'true';
    $scope.evntsLength;


    $scope.alert = {
        display:false,
        lastQuery:''
    };

    var _evnts = $scope.evnts;
    var _scope = $scope;

    if(_evnts.length === 0 ){
        $scope.alert.display = true;
    }


    $scope.filterEvnts = function () {
        //console.log(" FILTER E--------");
        $scope.$broadcast('setMarkers', {});
    }

    $scope.clearFilter = function () {

        //console.log("CLEAR FILTER --------");
        //console.log($scope.evnts.length);
        $scope.evnts = _evnts;
        //console.log($scope.locations.length);

        $scope.$broadcast('setMarkers', {filtered:$scope.locations, events:_evnts, map:map});
    }



    $scope.$on('setQuery', function ($scope, event) {

        _scope.query = event.desc;
        _scope.queryLength = (_scope.query.length > 0 ) ? 1 : 0;
    })

    $scope.$on('noData', function ($scope, event) {

        _scope.alert.display = event.showAlert;
        _scope.alert.lastQuery = event.lastQuery;
    })

    $scope.hideAlert = function(){
        _scope.alert.display = false;
    }

    $scope.$on('newDataset', function ($scope, event) {
        //console.log("++ newDataset ++++++++++");
        //console.log($scope);
        //console.log(_scope.evnts);
        //console.log(event.data);

        _scope.evnts = event.dataset.data;
        _scope.alert.lastQuery = event.lastQuery;
        _scope.$broadcast('setMarkers', {});

//        console.log(event);
//        console.log(_scope.alert);

    })

//    $scope.$watch('evnts.length', function (newValue, oldValue) {
//        console.log('WATCH  8| ');
//    });

    $scope.$watch('query', function (newValue, oldValue) {

        $scope.$broadcast('setMarkers', {});
        _scope.queryLength = (_scope.query.length > 0 ) ? 1 : 0;
    });

    $scope.$broadcast('setMarkers', {});
}

SearchCtrl.$inject = ['$rootScope', '$scope', '$filter', '$http', '$location'];