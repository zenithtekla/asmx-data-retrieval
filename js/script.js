(function () {
  'use strict';
var app = angular
    .module("myModule", [])
    .controller("myController", function ($scope, $http) {
        var params = "03/01/2016"; // var params = $scope.date;

        $http.jsonp("http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=" + params)
             .then(function (response) {
             	console.log(response);
             	// log-04/14: encountered error of requesting JSON in return but getting XML result => invalid token
                var o = {
                    name: response.data.Cust_Name,
                    status: response.data.Status,
                    timestamp: response.data.Acct_Date
                }
                $scope.customers = o;
             });

        $http.get("sample_data/sample_json.json")
             .then(function (response) {
             	console.log(response.data);
             	// log-04/14: 8 objects ~ 8 Marvel characters in return
                $scope.marvels = response.data;
             });
    });

// with JQuery conventionally - same JSONP error
$.ajax({
    url: 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=03/01/2016',
    dataType: 'JSONP',
    jsonpCallback: 'callback',
    type: 'GET',
    success: function (data) {
        console.log(data);
    }
});

}());