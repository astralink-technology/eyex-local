'use strict';

/* Controllers */

angular.module('eyexApp.setupController', []).
  controller('setupController', ['$scope', '$http', 'paramsServices', 'validationServices', function ($scope, $http, paramsServices, validationServices) {

        $( ".tb-mac-address" ).each(function( index ) {
            $(this).keyup(function(event){
                if ($(this).val().length == 2){
                    if ($(this).parent().next().find('[type="text"]').length > 0){
                        $(this).parent().next().find('[type="text"]')[0].focus();
                    }
                }
            });
        });

        var currentState = paramsServices.getParameterByName('step');
3
        if (!_currentStep && (currentState == 'company' || currentState == 'visitor-station' || currentState == 'office-station')) {
            window.location = "/eyex-lite/setup.php?step=complete";
        }else if (_currentStep != currentState && _currentStep == 'company'){
            window.location = "/eyex-lite/setup.php?step=company";
        }else if (_currentStep != currentState && _currentStep == 'visitor-station'){
            window.location = "/eyex-lite/setup.php?step=visitor-station";
        }else if (_currentStep != currentState && currentState != 'complete' && _currentStep == 'office-station'){
            window.location = "/eyex-lite/setup.php?step=office-station";
        }else if (currentState == 'company'){
            $("#step1").fadeIn('fast');
        }else if (currentState == 'visitor-station'){
            $("#step2").fadeIn('fast');
        }else if (currentState == 'office-station'){
            $("#step3").fadeIn('fast');
        }else if (currentState == 'complete'){
            $("#stepFinal").fadeIn('fast');
        }else{
            window.location = "/eyex-lite/setup.php?step=company";
        }

        //step 1
        $("#btn-next-step-1").click(function(){
            addCompany(function(){
                $("#step1").fadeOut('fast',function(){
                    window.location = "/eyex-lite/setup.php?step=visitor-station";
                })
            })
        })

        //step 2
        $("#btn-next-step-2").click(function(){
            addVisitorStation(function(){
                $("#step2").fadeOut('fast',function(){
                    window.location = "/eyex-lite/setup.php?step=office-station";
                })
            })
        })

        //step 3
        $("#btn-next-step-3").click(function(){
            addOfficeStation(function(){
                $("#step3").fadeOut('fast',function(){
                    window.location = "/eyex-lite/setup.php?step=complete";
                })
            })
        })
        $("#btn-skip-step-3").click(function(){
            $("#step3").fadeOut('fast',function(){
                window.location = "/eyex-lite/setup.php?step=complete";
            })
        })

        var addCompany = function(callback){
            var companyName = $scope.tbCompanyName;
            var companyEmail = $scope.tbCompanyEmail;
            var password = $scope.tbCompanyPassword;
            var confirmPassword = $scope.tbConfirmPassword;

            var companyNameValidation = validationServices.validateRequiredField(companyName);
            var companyEmailValidation = validationServices.validateEmail(companyEmail, true);
            var companyPasswordValidation = validationServices.validateRequiredLength(password, true, null, 8);
            var companyConfirmPasswordValidation = validationServices.validateRequiredSimilarField(confirmPassword, true, null, password);


            if (companyNameValidation){
                $("#tbCompanyNameError").show();
                $("#tbCompanyNameError").text(companyNameValidation);
            }else{
                $("#tbCompanyNameError").text('');
                $("#tbCompanyNameError").hide();
            }

            if (companyEmailValidation){
                $("#tbCompanyEmailError").show();
                $("#tbCompanyEmailError").text(companyEmailValidation);
            }else{
                $("#tbCompanyEmailError").text('');
                $("#tbCompanyEmailError").hide();
            }

            if (companyPasswordValidation){
                $("#tbCompanyPasswordError").show();
                $("#tbCompanyPasswordError").text(companyPasswordValidation);
            }else{
                $("#tbCompanyPasswordError").text('');
                $("#tbCompanyPasswordError").hide();
            }

            if (companyConfirmPasswordValidation){
                $("#tbConfirmPasswordError").show();
                $("#tbConfirmPasswordError").text(companyConfirmPasswordValidation);
            }else{
                $("#tbConfirmPasswordError").text('');
                $("#tbConfirmPasswordError").hide();
            }

            if (
                companyNameValidation ||
                companyEmailValidation ||
                companyPasswordValidation ||
                companyConfirmPasswordValidation
            ){
                return;
            }else {
                var parms = new Object();
                parms.Name = companyName;
                parms.Password = password;
                parms.Username = companyEmail;
                parms.Type = 'company';
                parms.AuthorizationLevel = 400;
                $http.post("/eyex-lite/controllers/users/addUser.php", parms)
                    .success(function(data){
                        if (!data.Error){
                            callback();
                        }else{
                            window && console.log(data);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }

        }

        var addVisitorStation = function(callback){
            var tbVsId1 = $scope.tbVsId1;
            var tbVsId2 = $scope.tbVsId2;
            var tbVsId3 = $scope.tbVsId3;
            var tbVsId4 = $scope.tbVsId4;
            var tbVsId5 = $scope.tbVsId5;
            var tbVsId6 = $scope.tbVsId6;
            var deviceName = $scope.tbVsDeviceName;

            var deviceId = "";

            if (tbVsId1 &&
                tbVsId2 &&
                tbVsId3 &&
                tbVsId4 &&
                tbVsId5 &&
                tbVsId6
            ){
                deviceId = $scope.tbVsId1 + $scope.tbVsId2 + $scope.tbVsId3 + $scope.tbVsId4 + $scope.tbVsId5 + $scope.tbVsId6;
            }

            var deviceValidation = validationServices.validateAlphaNumeric(deviceId, true);
            var deviceNameValidation = validationServices.validateRequiredField(deviceName);

            if (deviceNameValidation){
                $("#deviceVsNameError").show();
                $("#deviceVsNameError").text(deviceNameValidation);
            }else{
                $("#deviceVsNameError").text('');
                $("#deviceVsNameError").hide();
            }

            if (deviceValidation){
                $("#deviceIdError").show();
                $("#deviceIdError").text(deviceValidation);
            }else{
                $("#deviceIdError").text('');
                $("#deviceIdError").hide();
            }

            if (deviceId.length < 12){
                $("#deviceIdError").show();
                $("#deviceIdError").text("Invalid MAC Address");
                return;
            }else{
                $("#deviceIdError").text("");
                $("#deviceIdError").hide();
            }

            if (
                deviceValidation &&
                deviceId.length < 12
            ){
                return;
            }else{
                var parms = new Object();
                parms.DeviceId = deviceId;
                parms.DeviceName = deviceName;
                parms.DeviceType = 'VS';
                $http.post("/eyex-lite/controllers/device/addDevice.php", parms)
                    .success(function(data){
                        if (!data.Error){
                            callback();
                        }else{
                            window && console.log(data);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        var addOfficeStation = function(callback){
            var tbOsId1 = $scope.tbOsId1;
            var tbOsId2 = $scope.tbOsId2;
            var tbOsId3 = $scope.tbOsId3;
            var tbOsId4 = $scope.tbOsId4;
            var tbOsId5 = $scope.tbOsId5;
            var tbOsId6 = $scope.tbOsId6;
            var deviceName = $scope.tbOsDeviceName;

            var deviceId = "";

            if (tbOsId1 &&
                tbOsId2 &&
                tbOsId3 &&
                tbOsId4 &&
                tbOsId5 &&
                tbOsId6
            ){
                deviceId = $scope.tbOsId1 + $scope.tbOsId2 + $scope.tbOsId3 + $scope.tbOsId4 + $scope.tbOsId5 + $scope.tbOsId6;
            }


            var deviceValidation = validationServices.validateAlphaNumeric(deviceId, true);
            var deviceNameValidation = validationServices.validateRequiredField(deviceName);

            if (deviceNameValidation){
                $("#deviceOsNameError").show();
                $("#deviceOsNameError").text(deviceNameValidation);
            }else{
                $("#deviceOsNameError").text('');
                $("#deviceOsNameError").hide();
            }

            if (deviceValidation){
                $("#deviceIdOsError").show();
                $("#deviceIdOsError").text(deviceValidation);
                return;
            }else{
                $("#deviceIdOsError").text('');
                $("#deviceIdOsError").hide();
            }

            if (deviceId.length < 12){
                $("#deviceIdOsError").show();
                $("#deviceIdOsError").text("Invalid MAC Address");
                return;
            }else{
                $("#deviceIdOsError").text("");
                $("#deviceIdOsError").hide();
            }

            if (
                deviceValidation &&
                deviceNameValidation &&
                deviceId.length < 12
            ){
                return;
            }else{
                var parms = new Object();
                parms.DeviceId = deviceId;
                parms.DeviceName = deviceName;
                parms.DeviceType = 'OS';
                $http.post("/eyex-lite/controllers/device/addDevice.php", parms)
                    .success(function(data){
                        if (!data.Error){
                            callback();
                        }else{
                            window && console.log(data);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }
  }]);
