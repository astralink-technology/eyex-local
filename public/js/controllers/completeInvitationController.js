'use strict';

/* Controllers */

angular.module('eyexApp.completeInvitationController', []).
  controller('completeInvitationController', ['$scope','$http' ,'$location' , 'validationServices', function ($scope, $http, $location, validationServices) {
        //$("#tbPassword").focus();
        //
        //var invitationId = $location.search()['InvitationId'];
        //
        //$scope.acceptInvitation = function(){
        //    var password = $scope.tbPassword;
        //    var confirmPassword = $scope.tbConfirmPassword;
        //
        //    var passwordValidation = validationServices.validateRequiredLength(password, true, null, 6, 'Password cannot be shorter than 6 characters');
        //    var confirmPasswordValidation = validationServices.validateRequiredSimilarField(password, true, null, confirmPassword, 'Password does not match');
        //
        //
        //    if (passwordValidation){
        //        $scope.passwordError = true;
        //        $scope.valPassword = passwordValidation;
        //    }else{
        //        $scope.valPassword = "";
        //        $scope.passwordError = false;
        //    }
        //
        //    if (confirmPasswordValidation){
        //        $scope.confirmPasswordError = true;
        //        $scope.valConfirmPassword = confirmPasswordValidation;
        //    }else{
        //        $scope.valConfirmPassword = "";
        //        $scope.confirmPasswordError = false;
        //    }
        //
        //    if (passwordValidation || confirmPasswordValidation){
        //        $scope.overallError = true;
        //        $scope.valError = "Unable to accept invitation!";
        //        return;
        //    }else {
        //        $scope.overallError = false;
        //        $scope.valError = "";
        //
        //        var parms = new Object();
        //        parms.Password = password;
        //
        //        $("#completeInvitationModal").modal({
        //            backdrop: 'static'
        //            , keyboard: false
        //        });
        //
        //        var parms = new Object();
        //        parms.Password = password;
        //        parms.AuthenticationId = invitationId;
        //
        //        //ajax call to authenticate
        //        $http.post("/eyex/authentication/acceptInvitation", parms)
        //            .success(function(data){
        //                if (!data.Error){
        //                    window.location = "/settings/edit-profile";
        //                }else{
        //                    $("#completeInvitationModal").modal('hide');
        //                    $scope.overallError = true;
        //                    $scope.valError = JSON.stringify(data.ErrorDesc);
        //                }
        //            })
        //            .error(function(data) {
        //                window && console.log(data);
        //            });
        //    }
        //}
  }]);
