function UserCtrl($rootScope, $scope, $filter, $http) {

    var self = $scope;
    $scope.menu = {
        user:[
            {
                txt:'Tableau de bord',
                id:'user-tb',
                url:'#',
                active:'selected'
            },
            {
                txt:'Appels',
                id:'user-saved',
                url:'#stared',
                active:''
            },
            {
                txt:'Forages',
                id:'user-forages',
                url:'#forages',
                active:''
            },
            {
                txt:'Sentinelles',
                id:'user-watchers',
                url:'#watchers',
                active:''
            },
            {
                txt:'Modifier son profil',
                id:'user-profil',
                url:'#edit',
                active:''
            }
        ],
        admin:[
            {
                txt:'Gestion des utilisateurs',
                id:'admin',
                url:'admin',
                active:''
            }
        ]
    };

    $scope.$on('newRoute', function ($scope, route) {

        _.each( self.menu.user, function(item){
            item.active = ( item.url === route.hash )?'selected':'';
        });
    });
}