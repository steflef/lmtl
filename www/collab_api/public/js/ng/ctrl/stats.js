function StatsCtrl($rootScope, $scope, $log) {

    $scope.stats = stats;
    $scope.optionsData = stats.optionsData;
    $scope.statsData = stats.quickData[0];
    $scope.selection = "StatsCtrl OK";

    $scope.changeStats = function () {

        $scope.statsData = stats.quickData[$scope.test];
    };

    $scope.testClick = function (item) {

        $scope.statsData = stats.quickData[item];
    };

    $scope.listClick = function (field, code, desc) {

        if (field == 'priority') {
            desc = "P." + desc;
        }
        $rootScope.$broadcast('setQuery', {field:field, code:code, desc:desc});
    };
}