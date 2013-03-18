/*
 angular.module('myComponent',[])
 .directive('gMap', function(){
 console.log('OK');
 return{
 restrict:  'E',
 template:  '<span>Ma carte</span>'
 }
 })

 angular.module('appMain',['myComponent'])
 */

function MapCtrl($scope) {

    $scope.testMap = "Stef";

    /*
     $scope.$on('routeChangeSuccess', function($rootScope,params){
     console.log("$routeChangeSuccess LISTENER ============");
     console.log(params);
     //console.log(params.myRoute.idCall);
     console.log("========================");
     });
     */
}


angular.module('appMain', ['ngSanitize'])

    .config(function ($routeProvider, $locationProvider) {
        //$locationProvider.html5Mode(true);
        $routeProvider.
            when('/', {controller:HomeCtrl}).
            when('/details/:idCall', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/details.html", controller:DetailsCtrl}).
            when('/home', {controller:HomeCtrl}).
            otherwise({redirectTo:'/home'});
    })

    .directive('gMap', function () {
        return{
            restrict:'E',
            template:'<span>Ma carte</span>'
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
    });


function DetailsCtrl($rootScope, $scope, $routeParams, $log, $http, $location) {

    //$log.log("Detail CTRL ============");
    //$log.log($routeParams);
    //$log.log($routeParams.idCall);
    //$log.log("========================");
//    $rootScope.myTest = $routeParams;
//
//    $http.get(BASE_URL+"raoevnts/ref/" + $routeParams.idCall)
//        .success(function (data, status, headers, config) {
//
//            //console.log( status );
//            //console.log( data );
//            //console.log( headers() );
//            //console.log( config );
//
//            $scope.call = data;
//
//            $scope.seq = data.results.sequenceHtml
//
//            //console.log($scope.call.results.sequenceHtml);
//
//        })
//        .error(function (data, status, headers, config) {
//
//            //console.log( status );
//        });


}

//DetailsCtrl.$inject = ['$rootScope', '$routeParams'];

function HomeCtrl($rootScope, $routeParams) {

    //console.log("Home CTRL");
    //$rootScope.$broadcast('routeChange',{myRoute:$routeParams});
}