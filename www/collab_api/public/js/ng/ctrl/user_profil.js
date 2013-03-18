function UserProfilCtrl($rootScope, $scope, $filter, $http, $routeParams) {

    var _scope = $scope;
    $scope.baseUrl = gdata.app.baseUrl;
    $scope.userId = $routeParams.userId;
    $scope.subMenu = {
        user : '',
        public : 'active',
        subscriptions : ''
    };

    $scope.profil = {
        details:{ name:"Transmission des données en cours"},
        subscriptions:[],
        watchers:[]
    };
    $scope.message = '';
    $scope.alert={ display:false };

    $scope.getSubscriptionsLength = function(){

        return $scope.profil.subscriptions.length;
    }

    $scope.getWatchersLength = function(){

        return $scope.profil.watchers.length;
    }

    $scope.fetchProfilInfos = function () {
        $http.get($scope.baseUrl+'api/rao/profil/id/'+$routeParams.userId+'/format/json')
            .then(
            function (result) {
                if (result.status === 200) {
                    //console.log(result);
                    $scope.profil.details = result.data.profil;
                    $scope.profil.subscriptions = result.data.subscriptions;
                    $scope.profil.watchers = result.data.watchers;
                    $scope.alert.display = false;
                }
            },
            function (reason) {
                console.log(reason.headers());
                $scope.alert.display = true;
            }
        );
    }

    $rootScope.$broadcast('newRoute', {hash:'#watchers'});
    $scope.fetchProfilInfos();
}