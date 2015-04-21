'use strict';

/* Controllers */

angular.module('eyexApp.sipSettingsController', []).
  controller('sipSettingsController', ['$scope', '$http', 'validationServices', function ($scope, $http, validationServices) {


        var refreshSipForm = function(){
            $('#tbSipUsername').focus();
            $scope.tbSipUsername = '';
            $scope.tbSipHost = '';
            $scope.tbSipPassword = '';
        }

        var getSipDetails = function(){
            $("#loadSip").modal({
                keyboard:false,
                backdrop: 'static'
            });
            $http.get("/eyex-lite/controllers/sip/getSip.php")
                .success(function (data, status, headers, config) {
                    $("#loadSip").modal('hide');
                    if (data.RowsReturned > 0){
                        $scope.tbSipUsername = data.Data[0].username;
                        $scope.tbSipHost = data.Data[0].host;
                        $scope.sipId = data.Data[0].id;
                    }
                });
        }

        $scope.changeSipSettings = function(){
            var sipUsername = $scope.tbSipUsername;
            var sipHost = $scope.tbSipHost;
            var sipPassword = $scope.tbSipPassword;

            var usernameValidation = validationServices.validateRequiredField(sipUsername);
            var hostValidation = validationServices.validateRequiredField(sipHost);
            var sipPasswordValidation = validationServices.validateRequiredField(sipPassword);

            if (usernameValidation){
                $scope.sipUsernameError = true;
                $scope.valSipUsername = usernameValidation;
            }else{
                $scope.valSipUsername = "";
                $scope.sipUsernameError = false;
            }

            if (hostValidation){
                $scope.sipHostError = true;
                $scope.valSipHost = hostValidation;
            }else{
                $scope.valSipHost = "";
                $scope.sipHostError = false;
            }

            if (sipPasswordValidation){
                $scope.sipPasswordError = true;
                $scope.valSipPassword = sipPasswordValidation;
            }else{
                $scope.valSipPassword = "";
                $scope.sipPasswordError = false;
            }

            if (
                usernameValidation ||
                hostValidation ||
                sipPasswordValidation
            ){}else{
                $("#modalEditSip").modal({
                    backdrop: 'static'
                    , keyboard: false
                });
                $('input').blur();
                //update sip settings
                var parms= new Object();
                parms.Username = sipUsername;
                parms.Host = sipHost;
                parms.Password = sipPassword;
                if ($scope.sipId){
                    parms.SipProviderId = $scope.sipId;
                    $http.post("/eyex-lite/controllers/sip/updateSip.php", parms)
                        .success(function(data){
                            $("#modalEditSip").modal('hide');
                            console.log(data);
                            if (!data.Error){
                                $scope.updateSuccess = true;
                                $scope.successMessage = 'SIP Settings updated'
                                $scope.overallError = false;
                                $scope.valError = '';
                                refreshSipForm();
                                getSipDetails();
                            }else{
                                window && console.log(data.ErrorDesc);
                            }
                        })
                        .error(function(data) {
                            window && console.log(data);
                        });
                }else{
                    $http.post("/eyex-lite/controllers/sip/addSip.php", parms)
                        .success(function(data){
                            $("#modalEditSip").modal('hide');
                            if (!data.Error){
                                $scope.updateSuccess = true;
                                $scope.successMessage = 'SIP Settings updated'
                                $scope.overallError = false;
                                $scope.valError = '';
                                refreshSipForm();
                                getSipDetails();
                            }else{
                                window && console.log(data.ErrorDesc);
                            }
                        })
                        .error(function(data) {
                            window && console.log(data);
                        });
                }
            }
        }

        $scope.deleteSip = function(){
            if ($scope.sipId){
                var parms = new Object();
                parms.SipId = $scope.sipId;
                $http.post("/eyex-lite/controllers/sip/deleteSip.php", parms)
                    .success(function(data){
                        $("#modalEditSip").modal('hide');
                        if (!data.Error){
                            $scope.updateSuccess = true;
                            $scope.successMessage = 'SIP Settings updated'
                            $scope.overallError = false;
                            $scope.valError = '';
                            refreshSipForm();
                            getSipDetails();
                            $scope.sipId = null;
                        }else{
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        refreshSipForm();
        getSipDetails();
    }]);
