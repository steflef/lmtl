function UserWatchersCtrl($rootScope, $scope, $filter, $http) {

    var _scope = $scope;
    $scope.baseUrl = gdata.app.baseUrl;
    $scope.subMenu = {
        user : '',
        public : 'active',
        subscriptions : ''
    };
    $scope.orderList='created';
    $scope.reverseCheck= true;

    var subMenu = function(term){
        _.each(_scope.subMenu, function(element, index){
            _scope.subMenu[index] = (index === term)?'active' : '';
        });
    }

    $scope.watchers = {
        items:{},
        total:function(){
            return this.items.length;
        }
    };
    $scope.message = '';

    $scope.swapWatchers = function(term){
        console.log(term);
        switch (term){
            case 'subscriptions':
                _.each($scope.watchers.items, function(item){
                    item.display = (item.user_match > 0)?true:false;
                });
                subMenu(term);
                break;
            case 'user':
                console.log('case USER');
                _.each($scope.watchers.items, function(item){
                    item.display = (item.username === $scope.username)?true:false;
                });
                subMenu(term);
                break;
            case 'public':
            default:
                _.each($scope.watchers.items, function(item){
                    item.display = true;
                });
                subMenu(term);
        }
    }

    $scope.fetchWatchers = function () {
        $http.get($scope.baseUrl+'api/rao/watchers/format/json')
            .then(
            function (result) {
                if (result.status === 200) {

                    _.each(result.data.items, function(item){
                        item.display = true;
                    });

                    $scope.watchers.items = result.data.items;
                    $scope.username = result.data.username;
                }
            },
            function (reason) {
                console.log(reason.headers());
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
    $scope.fetchWatchers();
}