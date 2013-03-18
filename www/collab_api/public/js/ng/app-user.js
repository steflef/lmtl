angular.module('appUser', ['ngSanitize'])
    .config(function ($routeProvider, $locationProvider) {
        $routeProvider.
            when('/', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_tb_partial.html", controller:UserTbCtrl}).
            when('/stared', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_stared_partial.html", controller:UserStaredCtrl}).
            when('/forages', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_forage_partial.html", controller:UserForagesCtrl}).
            when('/watchers', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_watchers_partial.html", controller:UserWatchersCtrl}).
            when('/watcher/:watcherId', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_watcher_details_partial.html", controller:UserWatcherDetailsCtrl}).
            when('/profil/:userId', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_profil_partial.html", controller:UserProfilCtrl}).
            //when('/publicwatchers', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_watchers_public_partial.html", controller:UserPublicWatchersCtrl}).
            //when('/subscriptions', {templateUrl:"/gatlas_spring/public/js/ng/tmpl/user_watchers_subscriptions_partial.html", controller:UserSubscriptionsCtrl}).
            when('/edit', {templateUrl:"/gatlas_spring/user/get_edit", controller:UserEditCtrl}).
            otherwise({redirectTo:'/'});
    })

    .directive('map', function () {

        var linker = function(scope, element, attrs) {

            scope.map = new L.Map(attrs.id, {'scrollWheelZoom':false});
            scope.markersLayer = new L.LayerGroup();
            scope.markers = new L.MarkerClusterGroup({showCoverageOnHover:false});
            var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                osmAttrib = 'Map data © openstreetmap contributors',
                osm = new L.TileLayer(osmUrl, {minZoom:10, maxZoom:16, attribution:osmAttrib}),
                //mapCenter = new L.LatLng(origin.lat, origin.lon);
                mapCenter = new L.LatLng(gdata.location.lat, gdata.location.lon);
            scope.map
                .setView(mapCenter, 13)
                .addLayer(osm)
                .attributionControl.setPrefix('');

            //jQuery stuff
            // Todo Convert to Angular Model, DOM oriented right now :(
            var mapStretch = {
                min:function () {
                    $(".mapColumn").removeClass("maximazer");
                    $("#listColumn").removeClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-up").addClass("icon-chevron-down");

                    self.map.panBy(new L.Point(0, 100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.max();
                    });
                },
                max:function () {
                    $(".mapColumn").addClass("maximazer");
                    $("#listColumn").addClass("minimazer");
                    $(".map-pull").find("i").removeClass("icon-chevron-down").addClass("icon-chevron-up");

                    self.map.panBy(new L.Point(0, -100));
                    var innerself = this;
                    $(".map-pull").unbind("click").click(function () {
                        innerself.min();
                    });
                }
            };

            $(".map-pull").click(function () {
                mapStretch.max();
            });
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('typeahead', function(){

        return{
            restrict: 'A',
            link:function(scope, element, attrs) {

                $(element)
                    .typeahead({
                        source:function (query, process) {
                            return $.get(BASE_URL+'api/typeahead/query/format/json/q/' + query, null, function (data) {
                                return process(data.options);
                            });
                        }
                    })
                    .change(function () {
                        $(this).parent().submit();
                    });
            }
        }
    })
    .directive('displayModal', function () {
        var linker = function(scope, element, attrs) {
            var openDialog = function(){
                $("#myModal").modal('show');
            }
            element.bind('click',openDialog);
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .directive('hideModal', function () {
        var linker = function(scope, element, attrs) {
            var openDialog = function(){
                $("#myModal").modal('hide');
                //console.log(element.controller());
               // element.controller().setWatcher();
            }
            element.bind('click',openDialog);
        };

        return{
            restrict:'A',
            link:linker
        }
    })
    .filter('sub', function () {
        return function (text, from, to) {
            if (isNaN(from))
                from = 0;

            if (isNaN(to))
                to = 1;

            if (text.length < to) {
                return text;
            }
            else {
                return String(text).substring(from, to);
            }
        }
    })
    .filter('match', function () {
        return function (text, filter) {
            if (filter === undefined) {
                return text;
            } else {
                return text.replace(new RegExp(filter, 'gi'), '<span class="label label-success match">$&</span>');
            }
        }
    });

function UserEditCtrl($rootScope, $scope, $filter, $http) {

    $scope.submit = function(){
        //console.log("SUBMIT OK!");
        //console.log($scope);

        var message = '';
        var postData = {
            first_name:$scope.first_name,
            last_name:$scope.last_name,
            password:$scope.password,
            password_confirm :$scope.password_confirm,
            id:$scope.id,
            csrf:$scope.csrf
        };
        postData[$scope.csrf] = $scope.csrfval;
        var url = gdata.app.baseUrl+'user/get_edit';

        var xsrf = $.param(postData);
        //console.log(xsrf);

//        console.log(postData);
//        $http.post(
//            gdata.app.baseUrl+'user/get_edit',
//            postData
//        )
        $http({
            method: 'POST',
            url: url,
            data: xsrf,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
            .then(
            function (result) {
                if (result.status === 200) {

                    console.log(result);
                    console.log(result.headers());

                    $scope.message = result.data.message;
                    $scope.csrf = result.data.csrfkey;
                    $scope.csrfval =  result.data.csrf[$scope.csrf];
                }
            },
            function (reason) {

                console.log(reason);
            }
        );

    }

    $rootScope.$broadcast('newRoute', {hash:'#edit'});
}