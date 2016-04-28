(function () {
  'use strict';

var app = angular
    .module("myModule", [])
    .controller("myController", function ($scope, $http) {
        $http.defaults.headers.post['dataType'] = 'json';
        $http.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
        var params = "03/01/2016";

        $http.get("test.php")
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

        $http.get("sample_data/sample_json.json")
         .then(function (response) {
         	console.log(JSON.stringify(response.data));
            $scope.mutants = JSON.stringify(response.data);
            $scope.marvels = response.data;
         });
    });
}());