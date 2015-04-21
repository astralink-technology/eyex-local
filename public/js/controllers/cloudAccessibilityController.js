'use strict';

/* Controllers */

angular.module('eyexApp.cloudAccessibilityController', []).
  controller('cloudAccessibilityController', ['$scope', '$http',  'validationServices', function ($scope, $http, validationServices) {

        var setupPusherEnv = function(appId){
            // Enable pusher logging - don't include this in production
            Pusher.log = function(message) {
                if (window.console && window.console.log) {
                    window.console.log(message);
                }
            };
            //pusher test
            var pusher = new Pusher('8a7c33f8bae0cc471cac');
            var channel = pusher.subscribe(appId);
            channel.bind('eyex_sync', function(data) {
                console.log('connected');
                var parms = new Object();
                parms.Data = data;
                $http.post("/eyex-lite/controllers/sync/syncRemoteToLocal.php", JSON.stringify(parms))
                    .success(function(data){
                        window && console.log(data);
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            });
        }

        $scope.cloudAccountExists = false;
        $scope.cloudConnected = false;

        //load announcements
        var loadCloudAccessibility = function() {
            $("#loadCloudModal").modal({
                keyboard: false
                , backdrop: 'static'
            })
            $http.get("/eyex-lite/controllers/cloudAccess/getCloudInfo.php")
                .success(function (data, status, headers, config) {
                    $("#loadCloudModal").modal('hide');
                    if (data.RowsReturned > 0){
                        var cloudData = data.Data[0];
                        console.log(cloudData);
                        $scope.cloudSecret = cloudData.secret;
                        $scope.cloudUsername = cloudData.authentication_string;
                        $scope.cloudToken = cloudData.token;
                        $scope.cloudAccessId = cloudData.cloud_access_id;
                        if ($scope.cloudAccessId){
                            setupPusherEnv($scope.cloudAccessId);
                        }
                        $("#signupCloud").hide();
                    }else{
                        $("#signupCloud").show();
                    }
                });
        }

        loadCloudAccessibility();

        $scope.createCloudAccess = function(){
            var cloudUsername = $scope.tbCloudAccessUsername;
            var cloudPassword = $scope.tbCloudPassword;
            var cloudPasswordConfirm = $scope.tbCloudPasswordConfirm;

            var validateCloudUsername = validationServices.validateRequiredField(cloudUsername);
            var validateCloudPassword = validationServices.validateRequiredField(cloudPassword);
            var validateCloudPasswordConfirm = validationServices.validateRequiredSimilarField(cloudPasswordConfirm, true, null, cloudPassword, 'Password does not match');

            if (validateCloudUsername){
                $scope.cloudAccessUsernameError = true;
                $scope.valCloudAccessUsername = validateCloudUsername;
            }else{
                $scope.valCloudAccessUsername = "";
                $scope.cloudAccessUsernameError = false;
            }

            if (validateCloudPassword){
                $scope.cloudAccessPasswordError = true;
                $scope.valCloudAccessPassword = validateCloudPassword;
            }else{
                $scope.valCloudAccessPassword = "";
                $scope.cloudAccessPasswordError = false;
            }

            if (validateCloudPasswordConfirm){
                $scope.cloudAccessPasswordConfirmError = true;
                $scope.valCloudAccessPasswordConfirm = validateCloudPassword;
            }else{
                $scope.valCloudAccessPasswordConfirm = "";
                $scope.cloudAccessPasswordConfirmError = false;
            }

            if (
                validateCloudUsername ||
                validateCloudPassword ||
                validateCloudPasswordConfirm
            ){
                return;
            }else{
                $("#signupCloudModal").modal({
                    keyboard: false
                    , backdrop: 'static'
                })
                var parms = new Object();
                parms.DeviceId = $scope.cloudDeviceId;
                parms.Password = cloudPassword;
                parms.AuthenticationString = cloudUsername;
                parms.Name = _name;
                $http.post("/eyex-lite/controllers/cloudAccess/signupCloud.php", parms)
                    .success(function(data){
                        $("#signupCloudModal").modal('hide');
                        console.log(data);
                        if (!data.Error){
                            loadCloudAccessibility();
                        }else{
                            if (data.ErrorDesc == 'UserExists'){
                                $scope.cloudAccessUsernameError = true;
                                $scope.valCloudAccessUsername = 'User email taken';
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

    }]);
