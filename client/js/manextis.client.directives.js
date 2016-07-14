'use strict';
var URL_JSON_MANTIS_MANEX = "manextis_inc_2.php";

/*// Add to RegExp prototype http://aramk.com/blog/2012/01/16/preg_match_all-for-javascript/
RegExp.prototype.execAll = function(string) {
	var matches = [];
	var match = null;
	while ( (match = this.exec(string)) != null ) {
		var matchArray = [];
		for (var i in match) {
			if (parseInt(i) == i) {
				matchArray.push(match[i]);
			}
		}
		matches.push(matchArray);
	}
	return matches;
}*/

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
		        /*
		        // creator_id to be wired up.
		        var data = {
		            query: newVal,
		            creator_id: null
		        };
		        var config = {
		            params: { query : newVal},
		            headers : {'Accept' : 'application/json'}
		        };

		        $http.post( $scope.dirURL + "/" + URL_JSON_MANTIS_MANEX, config)

		        // see short-hand below for the preferred GET method - searching the right way, user can SAVE their searched link, HENCE reusability is made possible: */
		        if (newVal){
		        	$scope.xset = null;
	            	$scope.tset = null;
	            	$scope.shlog = null;
	            	$scope.error = null;
	            	$scope.stock = null;
	            	$scope.no_query = true;
	            	$scope.no_insertion = true;
			        $http.get( $scope.dirURL + "/" + URL_JSON_MANTIS_MANEX,
			        {
			            params: data
			        })
			        .then(function (res) {
			            $scope.Mocha = {
			                test: res.data.Mocha
			            };
			            // console.log(res.data);
			            var o = {};
			            var ob = {};
			            var oc = {};
			            if (typeof res.data ==='string'){
				            // res.data = res.data.replace(/(<pre>|<\/pre>)/g, '').trim();
				            /*// Ratkaisu 01
				            var matches = [];
				            res.data.replace(/<pre>([\s\S]+?)<\/pre>/g, function(){
				            	//arguments[0] is the entire match
				            	matches.push(arguments[1]);
				            });
				            res.data = matches[0];*/

				            var re = /<pre\s*[^>]*>([\S\s]*?)<\/pre>/i;
							var match = re.exec(res.data);
							res.data = match[1];
				            res.data = JSON.parse(res.data);
			            }
			            console.log(res.data);
		            	var pipe = res.data.sync.pipe || null;
		            	var fullhouse = res.data.sync.fullhouse || null;
		            	var shell = res.data.sync.shell || null;

		            	console.log("Main findings: ");
		            	console.log(pipe);
			            var jobj = (typeof pipe.response ==='string') ? JSON.parse(pipe.response) : pipe.response;
			            try {
			            	if (!jobj) throw new Error("Undefined object, response: ");
			            	$scope.no_result = Object.keys(jobj).length === 0 || jobj == null;


			            	if ($scope.no_result) throw new Error("Unable to map the result object, response: ");
				            jobj.map(function(d,idx){
				                o[idx] = {
				                    key: d.UNIQ_KEY || '',
				                    wo: d.WO_NO || '',
				                    so: d.SO_NO || '',
				                    due_date: d.DUE_DATE || '',
				                    assembly: d.ASSY_NO || '',
				                    revision: d.REVISION || '',
				                    qty: d.QTY || '',
				                    customer_po: d.CUST_PO_NO || '',
				                    customer_name: d.CUST_NAME || '',
				                    timestamp: Math.floor(Date.now() / 1000)
				                };
				            });

				            if (fullhouse){
				            	// console.log(fullhouse);
				            	if(!(fullhouse instanceof Array)) fullhouse = [fullhouse];
					            fullhouse.map(function(d,idx){
					                ob[idx] = {
					                    key: d.UNIQ_KEY || '',
					                    wo: d.WO_NO || '',
					                    so: d.SO_NO || '',
					                    due_date: d.DUE_DATE || '',
					                    assembly: d.ASSY_NO || '',
					                    revision: d.REVISION || '',
					                    qty: d.QTY || '',
					                    customer_po: d.CUST_PO_NO || '',
					                    customer_name: d.CUST_NAME || '',
					                    timestamp: Math.floor(Date.now() / 1000)
					                };
					                // console.log('client.Fullhouse ', ob);
					            });
				        	}

				        	if (shell){
				            	// console.log(shell);
				            	if(!(shell instanceof Array)) shell = [shell];
					            shell.map(function(d,idx){
					                oc[idx] = {
					                    bash: d.bash || '',
					                    timestamp: Math.floor(Date.now() / 1000)
					                };
					                // console.log('client.Shell ', oc);
					            });
				        	}
			            }
			            catch(e) {
			            	console.error(e.message, res.data);
			            }
			            finally {
			            	$scope.xset = o;
			            	$scope.tset = ob || null;
			            	$scope.shlog = oc || null;
			            	$scope.error = pipe.error || null;
			            	$scope.stock = pipe.stock || null;
			            	$scope.no_query = (!$scope.stock);
			            	$scope.no_insertion = (!$scope.tset);
			            }
			        });
			    }
    		});
		},
		templateUrl: 'manextis.client.view.resultFetch.html'
	};
}

angular
	.module('xt.directive', [])
	.directive('passingProfile', [passingProfile]);

function passingProfile(){
	return {
		restrict: 'E',
		scope: {
			data:'='
		},
		template: '<h3>{{data.name}}</h3>',
		controller: function($scope){
			console.log($scope.data);
		}
	}
}

/*
restrict: 'E', apply to element which <passing-profile> is an element
restrict: 'A', apply to attribute which <div passing-profile></div> or <div data-passing-profile></div>, passing-profile is an attribute role here, same as data=marvel, data is an attribute.
if scope were to be:
scope: {
	myData:'=data'
},
controller:function($scope){
	console.log($scope.myData); // use myData instead
}
for the sake of consistency, use marvel=marvel everywhere then.
See next commit

use template with template:'templates/profile.html'
*/