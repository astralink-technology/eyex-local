'use strict';

/* Controllers */

angular.module('eyexApp.landingController', []).
  controller('landingController', ['$scope', '$http', 'validationServices'
        , function ($scope, $http, validationServices) {

            var pusherLogs = new Array();

            _channel.bind('eyex_sync', function(data) {
                $scope.$apply(function(){
                    var log = new Object();
                    log.message = data + ' syncing triggered';
                    pusherLogs.push(log);
                    $scope.pusherLogs = pusherLogs;
                })
            });

        }]);
