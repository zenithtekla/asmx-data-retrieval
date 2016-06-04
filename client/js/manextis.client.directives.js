'use strict';
var URL_JSON_MANTIS_MANEX = "manextis_inc_2.php";

angular.module("manextis.directive",[])
.directive("resultFetch", ["$location", "$http", resultFetch ]);

function resultFetch($location, $http){
	return {
		link: function($scope){
			$scope.$watch('query',function(newVal, oldVal){
		        $location.search("query=" + newVal);
		        $scope.currSearch = $location.search();
		        var data = $scope.currSearch;
		        angular.extend(data, { Mocha: $scope.mochaBox});
		        console.log(data);
		        /*var data = {
		            query: newVal
		        };
		        var config = {
		            params: { query : newVal},
		            headers : {'Accept' : 'application/json'}
		        };

		        $http.post( $scope.dirURL + "/" + URL_JSON_MANTIS_MANEX, config)

		        // see short-hand below for the preferred GET method - searching the right way, user can SAVE their searched link, HENCE reusability is made possible: */
		        if (newVal)
		        $http.get( $scope.dirURL + "/" + URL_JSON_MANTIS_MANEX,
		        {
		            params: data
		        })
		        .then(function (res) {
		            $scope.Mocha = {
		                test: res.data.Mocha
		            };
		            var o = {};
		            var jobj = (typeof res.data.response ==='string') ? JSON.parse(res.data.response) : res.data.response;
		            $scope.no_result = (Object.keys(jobj).length === 0 || jobj == null) ? true : false;
		            jobj.map(function(d,idx){
		                o[idx] = {
		                    key: d.UNIQ_KEY || '',
		                    wo: d.WO_NO || '',
		                    so: d.SO_NO || '',
		                    due_date: d.DUE_DATE || '',
		                    assembly: d.ASSY_NO || '',
		                    revision: d.REVISION || '',
		                    qty: d.QTY || '',
		                    customer_id: d.CUST_PO_NO || '',
		                    customer_name: d.CUST_NAME || '',
		                    timestamp: Math.floor(Date.now() / 1000)
		                };
		            });
		            $scope.manextis = o;
		        });
    		});
		},
		templateUrl: 'manextis.client.view.resultFetch.html'
	};
}