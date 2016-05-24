(function () {
  'use strict';
var URL_ACQUIRE_DATE = "../model/manex_test_acquire_date.php";
var URL_SO_WO = "../model/manex_test_so_wo.php";
var URL_JSON_SAMPLE = "../sample_data/manex_sample_json.json";

// [1,2,3].map(n => console.log(n + 1));
var app = angular
    .module("myModule", [])
    .controller("myController", myCtrlFn);

function myCtrlFn ($scope, $http) {
    $http.defaults.headers.post['dataType'] = 'json';
    $http.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
    var params = "03/01/2016";

    $http.get(URL_ACQUIRE_DATE)
      .then(function (response) {
        console.log("8) data received");
        $scope.custs = response.data;
        var o = {};
        response.data.map(function(d,idx){
            o[idx] = {
                name: d.CUST_NAME,
                status: d.STATUS,
                timestamp: ((new Date(d.ACCT_DATE)).getTime() > 0) ? d.ACCT_DATE : (Date.parse(d.ACCT_DATE)/1000).toString()
            };
        });
        console.log(o);
        $scope.customers = o;
      });
    $http.get(URL_SO_WO)
      .then(function (response) {
        console.log("8) data received");
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
                customer_id: d.CUST_PO_NO,
                customer_name: d.CUST_NAME,
                timestamp: Math.floor(Date.now() / 1000)
            };
        });
        console.log(o);
        $scope.orders = o;
      });

    $http.get(URL_JSON_SAMPLE)
     .then(function (response) {
        console.log(JSON.stringify(response.data));
        $scope.mutants = JSON.stringify(response.data);
        $scope.marvels = response.data;
     });
};

}());