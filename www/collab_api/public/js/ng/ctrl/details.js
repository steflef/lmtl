function DetailsCtrl($rootScope, $scope, $http) {

    $scope.details = {
        callId:"",
        isStared:false,
        starButton:{
            isDisabled:false,
            btnClass:'default',
            btnText:'Ajouter à ma liste',
            btnTextVisibility:'',
            starClass:'default'
        }
    };

    $scope.starItem = function ($event) {
        console.log($event);
        console.log($scope.details.callId);


        // toggle
        if($scope.details.isStared === false){
            $scope.details.isStared = true;

            $http.put(
                '../../api/rao/star/format/json',
                {id:$scope.details.callId }
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
        }else{
            $scope.details.isStared = false;

            $http.delete('../../api/rao/star/id/'+$scope.details.callId+'/format/json')
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
    };

    $scope.$watch('details.isStared', function(newValue){

        if(newValue){
            $scope.details.starButton.btnClass ='info';
            $scope.details.starButton.starClass ='white';
            $scope.details.starButton.btnTextVisibility ='noDisplay'
        }else{
            $scope.details.starButton.btnClass ='default';
            $scope.details.starButton.starClass ='default';
            $scope.details.starButton.btnTextVisibility =''
        }
    });
}