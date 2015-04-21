'use strict';

/* Controllers */

angular.module('eyexApp.companyPasswordController', []).
  controller('companyPasswordController', ['$scope', '$http', 'validationServices', function ($scope, $http, validationServices) {
        $("#tbCurrentPassword").focus();

        var refreshChangePasswordForm = function(){
            $scope.tbCurrentPassword = '';
            $scope.tbNewPassword = '';
            $scope.tbConfirmPassword = '';
            $("#tbCurrentPassword").focus();
        }

        $scope.changeCompanyPassword = function(){
            var oldPassword = $scope.tbCurrentPassword;
            var newPassword = $scope.tbNewPassword;
            var confirmPassword = $scope.tbConfirmPassword;

            var oldPasswordValidation = validationServices.validateRequiredField(oldPassword);
            var newPasswordValidation = validationServices.validateRequiredLength(newPassword, true, null, 8, 'Password needs to bee 8 characters long');
            var confirmPasswordValidation = validationServices.validateRequiredSimilarField(confirmPassword, true, null, newPassword, 'Passwords do not match');

            if (oldPasswordValidation){
                $scope.passwordError = true;
                $scope.valPassword = oldPasswordValidation;
            }else{
                $scope.valPassword = "";
                $scope.passwordError = false;
            }

            if (newPasswordValidation){
                $scope.newPasswordError = true;
                $scope.valNewPassword = newPasswordValidation;
            }else{
                $scope.valNewPassword = "";
                $scope.newPasswordError = false;
            }

            if (confirmPasswordValidation){
                $scope.confirmPasswordError = true;
                $scope.valConfirmPassword = confirmPasswordValidation;
            }else{
                $scope.valConfirmPassword = "";
                $scope.confirmPasswordError = false;
            }

            if (
                oldPasswordValidation ||
                newPasswordValidation ||
                confirmPasswordValidation
            ){}else{
                $("#modalChangePassword").modal({
                    backdrop: 'static',
                    keyboard: false
                })
                $scope.overallError = false;
                $scope.passwordError = false;
                $scope.updateSuccess = false;

                $('input').each(function(){
                    $(this).blur();
                });

                //update sip settings
                var parms= new Object();
                parms.UserId = _user_id;
                parms.OldPassword = oldPassword;
                parms.NewPassword = newPassword;
                $http.post("/eyex-lite/controllers/account/changePassword.php", parms)
                    .success(function(data){
                        $("#modalChangePassword ").modal('hide');
                        if (!data.Error){
                            $scope.passwordError = false;
                            $scope.valPassword = '';
                            $scope.updateSuccess = true;
                            $scope.successMessage = 'Password Changed'
                            $scope.overallError = false;
                            $scope.valError = '';
                            refreshChangePasswordForm();
                        }else{
                            if (data.ErrorDesc == 'Old Password Incorrect'){
                                $scope.passwordError = true;
                                $scope.valPassword = 'Old password does not match.';
                            }else{
                                window && console.log(data.ErrorDesc);
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }
  }]);
