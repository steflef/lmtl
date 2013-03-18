function UserForagesCtrl($rootScope, $scope, $filter, $http) {

    $scope.baseUrl = gdata.app.baseUrl;
    var _scope = $scope;
    $scope.orderList='created';
    $scope.reverseCheck= true;
    $scope.forage = {
        items:{}
    };
    $scope.sentinelle = {
        name:'',
        note:'',
        public:{
            checked:true,
            isDisabled:true
        },
        subscriptions:{
            each:{checked:false,isDisabled:true},
            daily:{checked:true},
            monthly:{checked:false}
        },
        forage:{}
    };
    $scope.message = '';

    $scope.fetchForages = function () {

        $http.get($scope.baseUrl+'/api/rao/forages/format/json')
            .then(
            function (result) {
                if (result.status === 200) {
                    $scope.forage.items = result.data;
                }
            },
            function (reason) {
                console.log(reason.headers());
            }
        );
    }

    $scope.deleteForage = function(id, index){

        $scope.forage.items.splice(index, 1);

        $http.delete($scope.baseUrl+'/api/rao/forage/id/'+ id +'/format/json')
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

    $scope.buildSentinelle = function(index){

        $scope.sentinelle.forage =  $scope.forage.items[index];
    };

    $scope.setWatcher = function(){

        //var xsrf = $.param($scope.sentinelle);
        //var public = toBinary($scope.sentinelle.public.checked);


        $http.put(
            $scope.baseUrl+'api/rao/watcher/format/json',
            {
                name:$scope.sentinelle.name,
                note:$scope.sentinelle.note,
                forage_uri:$scope.sentinelle.forage.uri,
                public:toBinary($scope.sentinelle.public.checked),
                sub_each:toBinary($scope.sentinelle.subscriptions.each.checked),
                sub_daily:toBinary($scope.sentinelle.subscriptions.daily.checked),
                sub_monthly:toBinary($scope.sentinelle.subscriptions.monthly.checked)
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
    };

    var toBinary = function(item){
        return (item === true)? 1:0;
    }


    $rootScope.$broadcast('newRoute', {hash:'#forages'});
    $scope.fetchForages();
}