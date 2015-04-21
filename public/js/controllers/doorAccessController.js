'use strict';

/* Controllers */

angular.module('eyexApp.doorAccessController', []).
  controller('doorAccessController', ['$scope', '$http', 'validationServices', function ($scope, $http, validationServices) {
        var broadcastFeatures = function(callback){
            $http.get("/OfficeStation/reloadFeaturesCode.php")
                .success(function (data, status, headers, config) {
                    callback();
                }).error(function(data){
                    console.log(data);
                    callback();
                });
        }
        var loadDoorAccessConfig = function(){
            $("#loadDoorAccess").modal({
                keyboard:false,
                backdrop: 'static'
            });
            $http.get("/eyex-lite/controllers/features/getFeatures.php")
                .success(function (data, status, headers, config) {
                    $("#loadDoorAccess").modal('hide');
                    console.log(data);
                    if (data.RowsReturned > 0){
                        $scope.featuresId = data.Data[0].id;
                        var remoteDoorDigit = data.Data[0].remote_door;
                        var localDoorDigit = data.Data[0].local_door;

                        var remoteDoorDigit1;
                        var remoteDoorDigit2;
                        var remoteDoorDigit3;
                        var remoteDoorDigit4;
                        if (remoteDoorDigit){
                            var remoteDoorDigitArray = remoteDoorDigit.toString().split("");
                            if (remoteDoorDigitArray[0]){
                                remoteDoorDigit1 = remoteDoorDigitArray[0];
                                $scope.tbRemote1 = remoteDoorDigit1;
                            }else{
                                $scope.tbRemote1 = '';
                            }
                            if (remoteDoorDigitArray[1]){
                                remoteDoorDigit2 = remoteDoorDigitArray[1];
                                $scope.tbRemote2 = remoteDoorDigit2;
                            }else{
                                $scope.tbRemote2 = '';
                            }
                            if (remoteDoorDigitArray[2]){
                                remoteDoorDigit3 = remoteDoorDigitArray[2];
                                $scope.tbRemote3 = remoteDoorDigit3;
                            }else{
                                $scope.tbRemote3 = '';
                            }
                            if (remoteDoorDigitArray[3]){
                                remoteDoorDigit4 = remoteDoorDigitArray[3];
                                $scope.tbRemote4 = remoteDoorDigit4;
                            }else{
                                $scope.tbRemote4 = '';
                            }
                        }

                        var localDoorDigit1;
                        var localDoorDigit2;
                        var localDoorDigit3;
                        var localDoorDigit4;
                        if (localDoorDigit){
                            var localDoorDigitArray = localDoorDigit.toString().split("");
                            if (localDoorDigitArray[0]){
                                localDoorDigit1 = localDoorDigitArray[0];
                                $scope.tbLocal1 = localDoorDigit1;
                            }else{
                                $scope.tbLocal1 = '';
                            }
                            if (localDoorDigitArray[1]){
                                localDoorDigit2 = localDoorDigitArray[1];
                                $scope.tbLocal2 = localDoorDigit2;
                            }else{
                                $scope.tbLocal2 = '';
                            }
                            if (localDoorDigitArray[2]){
                                localDoorDigit3 = localDoorDigitArray[2];
                                $scope.tbLocal3 = localDoorDigit3;
                            }else{
                                $scope.tbLocal3 = '';
                            }
                            if (localDoorDigitArray[3]){
                                localDoorDigit4 = localDoorDigitArray[3];
                                $scope.tbLocal4 = localDoorDigit4;
                            }else{
                                $scope.tbLocal4 = ''
                            }
                        }
                    }
                });
        }

        $scope.editDoorAccess = function(){
            var tbRemote1 = $scope.tbRemote1;
            var tbRemote2 = $scope.tbRemote2;
            var tbRemote3 = $scope.tbRemote3;
            var tbRemote4 = $scope.tbRemote4;
            var tbLocal1 = $scope.tbLocal1;
            var tbLocal2 = $scope.tbLocal2;
            var tbLocal3 = $scope.tbLocal3;
            var tbLocal4 = $scope.tbLocal4;
            var remote = "";
            if (tbRemote1) remote += String(tbRemote1);
            if (tbRemote2) remote += String(tbRemote2);
            if (tbRemote3) remote += String(tbRemote3);
            if (tbRemote4) remote += String(tbRemote4);
            var local = "";
            if (tbLocal1) local += String(tbLocal1);
            if (tbLocal2) local += String(tbLocal2);
            if (tbLocal3) local += String(tbLocal3);
            if (tbLocal4) local += String(tbLocal4);

            //validate integer fields now
            var validateRemote = validationServices.validateRequiredInteger(remote, null);
            var validateLocal = validationServices.validateRequiredInteger(local, null);

            if (validateRemote){
                $scope.remoteError = true;
                $scope.valRemote = validateRemote;
            }else{
                $scope.valRemote = '';
                $scope.remoteError = false;
            }

            if (validateLocal){
                $scope.localError = true;
                $scope.valLocal = validateLocal;
            }else{
                $scope.valLocal = '';
                $scope.localError = false;
            }

            if (
                validateRemote ||
                validateLocal
            ){
                return;
            }else{
                $("#editingDoorAccessSettings").modal({
                    keyboard:false,
                    backdrop: 'static'
                });
                var parms = new Object();
                parms.RemoteDoor = remote;
                parms.LocalDoor = local;
                if ($scope.featuresId){
                    parms.FeaturesId = $scope.featuresId;
                    $http.post("/eyex-lite/controllers/features/updateFeatures.php", parms)
                        .success(function(data){
                            $("#editingDoorAccessSettings").modal('hide');
                            console.log(data);
                            broadcastFeatures(function() {
                                loadDoorAccessConfig();
                            })
                        })
                        .error(function(data) {
                            window && console.log(data);
                        });
                }else{
                    $http.post("/eyex-lite/controllers/features/addFeatures.php", parms)
                        .success(function(data){
                            $("#editingDoorAccessSettings").modal('hide');
                            console.log(data);
                            broadcastFeatures(function() {
                                loadDoorAccessConfig();
                            })
                        })
                        .error(function(data) {
                            window && console.log(data);
                        });
                }
            }
        }

        loadDoorAccessConfig();
    }]);
