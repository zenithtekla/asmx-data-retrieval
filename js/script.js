// http://www.codeproject.com/Articles/777284/Consuming-JSON-ASMX-Web-Services-with-AngularJS-A
// webconfig http://stackoverflow.com/questions/11088294/asp-net-asmx-web-service-returning-xml-instead-of-json
/* Py.
$.get(url, {var1: parameter1, var2: parameter2}, function(data){
    data = JSON.parse($(data).find("string").text());
    alert("data.source: " + data.source);
});

http://stackoverflow.com/questions/10824471/asmx-web-service-returning-xml-instead-of-json-trying-to-remove-string-xmlns
*/

(function () {
  'use strict';
var app = angular
    .module("myModule", [])
    .controller("myController", function ($scope, $http) {

        var params = "03/01/2016"; // var params = $scope.date;

        /*$http.get("test.php")
             .then(function (response) {
                console.log("8) data received");
                $scope.custs = response.data;
             	// log-04/15: No JSONP error but no result returned through HTTP request
                var o = {};
                // var i = 0;
                response.data.map(function(d,idx){
                    o[idx] = {
                        name: d.CUST_NAME,
                        status: d.STATUS,
                        timestamp: d.ACCT_DATE
                    };
                    // i++;
                    // o = o.push(tempo);
                });
                console.log(o);
                $scope.customers = o;
             });*/

        $http.get("http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=03/01/2016&status=")
             .then(function (response) {
                console.log("8) data received");
                $scope.custs = response.data;
              // log-04/15: No JSONP error but no result returned through HTTP request
                var o = {};
                // var i = 0;
                response.data.map(function(d,idx){
                    o[idx] = {
                        name: d.CUST_NAME,
                        status: d.STATUS,
                        timestamp: d.ACCT_DATE
                    };
                    // i++;
                    // o = o.push(tempo);
                });
                console.log(o);
                $scope.customers = o;
             });

        $http.get("sample_data/sample_json.json")
         .then(function (response) {
            // console.log("8) data received");
         	console.log(JSON.stringify(response.data));
         	// log-04/13: 8 objects ~ 8 Marvel characters in return
            $scope.mutants = JSON.stringify(response.data);
            $scope.marvels = response.data;
         });
    });

// with JQuery conventionally - same as no result from JQuery AJAX request
/* $.ajax({
    url: 'http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate?Acct_Date=03/01/2016&status=',
    dataType: 'JSONP',
    jsonpCallback: 'callback',
    type: 'GET',
    success: function (data) {
        console.log("collect data ", data);
    }
});*/

/*$.ajax({
    crossDomain: true,
    contentType: "application/json; charset=utf-8",
    url: "http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate",
    data: { Acct_Date: "03/01/2016", status: "" }, // example of parameter being passed
    dataType: "jsonp",
    success: onDataReceived
});*/


/*var ReceiveCustomerByCutOffDate = function(str) {
    var url =  "https://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate";
    var payload = { Acct_Date: str, status: "" };
    $.ajax({
      type: "POST",
      url: url,
      data: JSON.stringify(payload),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      success: function (data) {
          alert(data.d);
      },
      error: function (xmlHttpRequest, textStatus, errorThrown) {
          console.log(xmlHttpRequest.responseText);
          console.log(textStatus);
          console.log(errorThrown);
      }
    });
};
ReceiveCustomerByCutOffDate("03/01/2016"); */

}());

/*var url =  "http://erp-2/ews/ManexWebService.asmx/GetCustomerByCutOffDate";
with (new ActiveXObject("Microsoft.XmlHttp")) {
    open('GET', url, false);
    send('');
    var data = responseText;
    console.log(data);
    with (new ActiveXObject("ADODB.Stream")) {
        Open();
        Type = 2; // adTypeText
        Charset = 'utf-8'; // specify correct encoding
        WriteText(data);
        SaveToFile("page.html", 2);
        Close();
    }
} */