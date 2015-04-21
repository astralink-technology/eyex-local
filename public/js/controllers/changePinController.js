'use strict';

/* Controllers */

angular.module('eyexApp.changePinController', []).
  controller('changePinController', ['$scope', '$http', 'validationServices', '$rootScope', function ($scope, $http, validationServices, $rootScope) {
        $('#tbCurrentPin').focus();

        var refreshChangePasswordForm = function(){
            $scope.tbCurrentPin = '';
            $scope.tbNewPin = '';
            $scope.tbConfirmPin = '';
            $('#tbCurrentPin').focus();
        }

        $scope.changePin = function(){
            var oldPin = $scope.tbCurrentPin;
            var newPin = $scope.tbNewPin;
            var confirmPin = $scope.tbConfirmPin;

            var newPinValidation = validationServices.validateRequiredLength(newPin, true, null, 4, 'PIN needs to be at least 4 characters long');
            var confirmPinValidation = validationServices.validateRequiredSimilarField(confirmPin, true, null, newPin, 'New PINs does not match');

            if (newPinValidation){
                $scope.newPinError = true;
                $scope.valNewPin = newPinValidation;
            }else{
                $scope.valNewPin = "";
                $scope.newPinError = false;
            }

            if (confirmPinValidation){
                $scope.confirmPinError = true;
                $scope.valConfirmPin = confirmPinValidation;
            }else{
                $scope.valConfirmPin = "";
                $scope.confirmPinError = false;
            }

            if (
                newPinValidation ||
                confirmPinValidation
            ){}else{
                $("#modalChangePin").modal({
                    backdrop: 'static',
                    keyboard: false
                })
                $scope.overallError = false;
                $scope.pinError = false;
                $scope.updateSuccess = false;

                $('input').each(function(){
                    $(this).blur();
                });

                //check if pin matches
                var pinParms = new Object();
                pinParms.UserId = _user_id;
                pinParms.OldPin = oldPin;
                pinParms.NewPin = newPin;
                $http.post("/eyex-lite/controllers/users/updatePin.php", pinParms)
                    .success(function (data) {
                        console.log(data);
                        if (!data.Error) {
                            $("#modalChangePin").modal('hide');
                            $scope.overallError = false;
                            $scope.pinError = false;
                            $scope.newPinError = false;
                            $scope.successMessage = 'PIN changed successfully';
                            $scope.updateSuccess = true;
                            refreshChangePasswordForm();
                        } else {
                            $("#modalChangePin").modal('hide');
                            $("#tbCurrentPin").focus();
                            if (data.ErrorDesc == 'Old PIN Incorrect'){
                                $scope.pinError = true;
                                $scope.valPin = data.ErrorDesc;
                            }else if (data.ErrorDesc == 'PIN In Use'){
                                $scope.newPinError = true;
                                $scope.valNewPin = 'PIN already taken';
                            }else{
                                window && console.log(data.ErrorDesc);
                            }
                        }
                    })
                    .error(function (data) {
                        window && console.log(data);
                    });

            }
        }
  }]);
