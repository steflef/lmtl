function ToolBarCtrl($rootScope, $scope, $filter, $http) {

    var self = $scope;
    //$scope.baseUrl = 'http://localhost/gatlas_spring/';
    $scope.baseUrl = './';
    $scope.appTitle = 'LMTL';
    $scope.selectedIcon = '';
    $scope.selectedItem = '';

    $scope.topmenu = [
        {   id:'tb',
            title:'Tableau de bord',
            url:'',
            icon:'icon-map-marker',
            selected:''
        },
        {   id:'upload',
            title:'Téléchargez!',
            url:'upload',
            icon:'icon-download',
            selected:''
        },
        {   id:'edit',
            title:'Édition',
            url:'edit',
            icon:'icon-edit',
            selected:''
        }
    ];

    $scope.$on('newMenu', function ($scope, route) {
        _.each( self.topmenu, function(item){
            if(item.id === route.id){
                item.selected = 'active';
                self.selectedIcon = item.icon;
                self.selectedItem = item.title;
                self.selectedUrl = item.url;
            }else{
                item.selected = '';
            }
        });
    });
}