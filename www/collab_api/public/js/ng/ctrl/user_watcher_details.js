function UserWatcherDetailsCtrl($rootScope, $scope, $filter, $http, $routeParams) {

    var _scope = $scope;
    $scope.baseUrl = gdata.app.baseUrl;
    $scope.watcherId = $routeParams.watcherId;
    $scope.subMenu = {
        user : '',
        public : 'active',
        subscriptions : ''
    };
    $scope.alert={ display:false };

    $scope.watcher = {
        details:{ name:"Transmission des données en cours"},
        subscribers:[]
    };
    $scope.message = '';

    $scope.getSubscribersLength = function(){

        return $scope.watcher.subscribers.length;
    }

    $scope.fetchWatcherInfos = function () {
        $http.get($scope.baseUrl+'api/rao/watcher/id/'+$routeParams.watcherId+'/format/json')
            .then(
            function (result) {
                if (result.status === 200) {
                    console.log(result);
                    $scope.watcher.details = result.data.watcher;
                    $scope.watcher.subscribers = result.data.subscribers;
                    $scope.alert.display = false;
                }
            },
            function (reason) {
                console.log(reason.headers());
                $scope.alert.display = true;
            }
        );
    }

    $scope.unsubscribe = function(id, index){

        $scope.forage.items.splice(index, 1);

        $http.delete($scope.baseUrl+'api/rao/watcher/id/'+ id +'/format/json')
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


    $scope.subscribe = function(id, index){

        $scope.forage.items.splice(index, 1);

        $http.put(
            $scope.baseUrl+'api/rao/watcher/format/json',
            {id:id}
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

    $rootScope.$broadcast('newRoute', {hash:'#watchers'});
    $scope.fetchWatcherInfos();
}