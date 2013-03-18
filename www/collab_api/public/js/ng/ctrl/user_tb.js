function UserTbCtrl($rootScope, $scope, $filter, $http) {

    $rootScope.$broadcast('newRoute', {hash:'#'});
}