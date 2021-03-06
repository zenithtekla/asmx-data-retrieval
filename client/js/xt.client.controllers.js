(function () {
  'use strict';
var URI_ACQUIRE_DATE = 'model/xt_test_acquire_date.php';
var URI_SO_WO = 'model/xt_test_so_wo.php';
var URI_JSON_SAMPLE = 'model/sample_data/xt_sample_json.json';
var URI_TRUNCATE = 'truncate.php';

// [1,2,3].map(n => console.log(n + 1));
var app = angular
    .module('myApp', ['xt.filter', 'xt.directive1', 'xt.directive2', 'xt.directive3', 'xt.directive4', 'angular-momentjs'])
    .controller('myController', ['$scope', '$http', '$location', '$interval', '$timeout', myCtrlFn]);

function myCtrlFn ($scope, $http, $location, $interval, $timeout) {
    $scope.name = "Ellen Page";
    $http.defaults.headers.post['dataType'] = 'json';
    $http.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    var relUrl = $location.absUrl();
    relUrl = relUrl.substring(0, relUrl.lastIndexOf('/'));
    $scope.dirURL = relUrl;
    console.log("my current#Url: ", window.location.href);

    $scope.theInvisible = true;
    $scope.sampleToggle = function() {
        $scope.theInvisible = !$scope.theInvisible;
    };
    $scope.init = $interval( function(){
        $scope.Time = Math.round(new Date().getTime()/1000.0);
        $scope.formattedTime = $scope.Time;
    }, 1000);

    $scope.truncate = function(){
        $http.get( $scope.dirURL + "/" + URI_TRUNCATE,
        {
            params: {truncate : 1}
        })
        .then(function (res) {
            console.clear();
            $scope.truncated = res.data;
            $timeout(function(){
                $scope.truncated = '';
            }, 1300);
        });
    };

    $scope.$watch('field10_model',function(newVal, oldVal){
        console.log(newVal, oldVal);
    });
    $scope.$watch('entry.date_received',function(newVal){
        console.log(newVal);
    });
    /* ---------------------------------------------------------
    Sample dataDump
    ----------------------------------------------------------*/
    $http.get(relUrl + "/" + URI_ACQUIRE_DATE)
      .then(function (response) {
        $scope.custs = response.data;
        var o = {};
        response.data.map(function(d,idx){
            o[idx] = {
                name: d.CUST_NAME,
                status: d.STATUS,
                timestamp: ((new Date(d.ACCT_DATE)).getTime() > 0) ? d.ACCT_DATE : (Date.parse(d.ACCT_DATE)/1000).toString()
            };
        });
        $scope.customers = o;
      });
    $http.get(relUrl + "/" + URI_SO_WO)
      .then(function (response) {
        $scope.custs = response.data;
        var o = {};
        response.data.map(function(d,idx){
            o[idx] = {
                key: d.UNIQ_KEY,
                wo: d.WO_NO,
                so: d.SO_NO,
                due_date: d.DUE_DATE,
                assembly: d.ASSY_NO,
                revision: d.REVISION,
                qty: d.QTY,
                customer_po: d.CUST_PO_NO,
                customer_name: d.CUST_NAME,
                timestamp: Math.floor(Date.now() / 1000)
            };
        });
        $scope.orders = o;
      });
    $http.get(relUrl + "/" + URI_JSON_SAMPLE)
     .then(function (response) {
        $scope.mutants = JSON.stringify(response.data);
        $scope.marvels = response.data;
    });
};

}());

