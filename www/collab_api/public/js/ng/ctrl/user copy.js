function UserCtrl($rootScope, $scope, $filter, $http) {

    $scope.alert = {
        display:false,
        lastQuery:''
    };

    $scope.user = {
    }

    $scope.forages = {};

    var _scope = $scope;

    $scope.adminCreate = function () {
    }

    $scope.fetchCalls = function () {

        $http.get('../api/rao/usercalls/format/json')
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

    $scope.fetchForages = function () {

        $http.get('../api/rao/forages/format/json')
            .then(
            function (result) {
                if (result.status === 200) {
                    $scope.forage = result.data;
                }
            },
            function (reason) {
                console.log(reason.headers());
            }
        );
    }

    $scope.deleteForage = function(id, index){
        $scope.forages.splice(index, 1);

        $http.delete('../api/rao/forage/id/'+ id +'/format/json')
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

    $scope.$on('serverUnstar', function ($scope, params ) {

        $http.delete('../api/rao/star/id/'+params.id +'/format/json')
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

    $scope.fetchCalls();
}