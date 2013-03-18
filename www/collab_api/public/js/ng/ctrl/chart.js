function ChartCtrl($rootScope, $scope, $filter) {

    $scope.chart = chart;
    var lastVal = $scope.chart[$scope.chart.length - 1];
    var firstVal = $scope.chart[0];
    //$scope.dt = lastVal.d + " " + lastVal.h + "H " + lastVal.v + " appel(s)";
    $scope.dt = lastVal.h + "H" + lastVal.d;
    $scope.d = firstVal.d;
    $scope.t = firstVal.h + "H";
    $scope.dayCalls = lastVal.v + " appel(s)";
    $scope.yesterday = firstVal.d;

    $scope.changeInfos = function (params) {

        $scope.dt = params[0] + " " + params[1] + "H";
        $scope.dayCalls = params[2] + " appel(s)";
        $scope.d = params[0];
        $scope.t = params[1] + "H";
    };

    $scope.changeQuery = function (param) {

        $rootScope.$broadcast('setQuery', {desc:param});
    };
}