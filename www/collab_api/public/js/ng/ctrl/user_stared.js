function UserStaredCtrl($rootScope, $scope, $filter, $http) {

    $scope.baseUrl = gdata.app.baseUrl;
    var _scope = $scope;

    $scope.adminCreate = function () {
    }

    $scope.fetchCalls = function () {

        $http.get($scope.baseUrl+'/api/rao/usercalls/format/json')
        .then(
            function (result) {
                if (result.status === 200) {

                    $rootScope.$broadcast('noData', {showAlert:false});
                    $rootScope.$broadcast('newDataset', {dataset:result.data, lastQuery:''});
                }
            },
            function (reason) {

                $rootScope.$broadcast('noData', {showAlert:true, headers:reason.headers(), lastQuery:''});
            }
        );
    }

    $scope.$on('serverUnstar', function ($scope, params ) {

        $http.delete($scope.baseUrl+'/api/rao/star/id/'+params.id +'/format/json')
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
    });

    $rootScope.$broadcast('newRoute', {hash:'#stared'});
    $scope.fetchCalls();
}