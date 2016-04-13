(function () {
  'use strict';
var app = angular
    .module("myModule", [])
    .controller("myController", function ($scope, $http) {
        $http.post("erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate")
             .then(function (response) {
             	console.log(response.data);
             	// log-04/13: encountered error 404 (Not Found)
                $scope.customers = response.data;
             });

        $http.get("sample_data/sample_json.json")
             .then(function (response) {
             	console.log(response.data);
             	// log-04/13: 8 objects ~ 8 Marvel characters in return
                $scope.marvels = response.data;
             });
    });
}());