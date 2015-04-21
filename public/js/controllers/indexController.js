'use strict';

/* Controllers */

angular.module('eyexApp.indexController', []).
    controller('indexController', ['$scope', '$http', 'validationServices', function ($scope, $http, validationServices) {
        $('#tbEmail').focus();

        var parms = new Object();
        var pusherObject = new Object();
        pusherObject.type = 'sync-announcements';
        pusherObject.ownerId = 'V0L2XLKE-NMSZVA1K-YMQT23HJ';
        parms.Data = JSON.stringify(pusherObject);
        $http.post("/eyex-lite/controllers/sync/syncRemoteToLocal.php", parms)
            .success(function(data){
                console.log(data);
            })
            .error(function(data) {
                console.log(data);
            });

        $scope.login = function(){
            var email = $scope.tbEmail;
            var password = $scope.tbPassword;

            var passwordValidation = validationServices.validateRequiredField(password);
            var emailValidation = validationServices.validateEmail(email, true);

            if (emailValidation){
                $scope.emailError = true;
                $scope.valEmail = emailValidation;
            }else{
                $scope.valEmail = "";
                $scope.emailError = false;
            }

            if (passwordValidation){
                $scope.passwordError = true;
                $scope.valPassword = passwordValidation;
            }else{
                $scope.valPassword = "";
                $scope.passwordError = false;
            }

            if (passwordValidation || emailValidation){
                $scope.overallError = true;
                $scope.valOverallError = "Fill up required fields!";
                return;
            }else {
                $scope.overallError = false;
                $scope.valOverallError = "";

                var parms = new Object();
                parms.Email = email;
                parms.Password = password;

                $('input').each(function(){
                    $(this).blur();
                });

                $("#loginModal").modal({
                    backdrop: 'static'
                    , keyboard: false
                });

                var parms = new Object();
                parms.AuthenticationString = email;
                parms.Password = password;
                
                //ajax call to authenticate
                $http.post("/eyex-lite/controllers/account/login.php", parms)
                    .success(function(data){
                        console.log(data);
                        if (data.RowsReturned > 0){
                            var userDetails = data.Data[0];
                            var username = userDetails.username;
                            var authLevel = parseInt(userDetails.authorization_level);
                            if (authLevel == 300){
                                window.location = "/eyex-lite/profile.php";
                            }else if (authLevel >= 400){
                                window.location = "/eyex-lite/company.php";
                            }
                        }else{
                            $scope.overallError = true;
                            $scope.valOverallError = 'Authentication Failed';
                            $("#loginModal").modal('hide');
                        }
                    })
                    .error(function(data) {
                        $("#loginModal").modal('hide');
                        window && console.log(data);
                    });
            }
        }
    }]);