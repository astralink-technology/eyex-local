'use strict';

/* Controllers */

angular.module('eyexApp.accountSettingsController', []).
  controller('accountSettingsController', ['$scope', '$http', 'validationServices', 'countryServices', function ($scope, $http, validationServices, countryServices) {
        $("#tbPrimaryEmail").focus();
        var countries = countryServices.getCountries();
        for (var c = 0; c < countries.length; c++){
            var html = '<option value="' + countries[c].code + '">' + countries[c].name + ' (+' + countries[c].code + ')</option>'
            $('#ddlCountryCode').append(html);
            $('#ddlCountryCode2').append(html);
        };


        var getAccountDetails = function(){
            $('#loadAccount').modal({
                backdrop: 'static',
                keyboard: false
            })
            //get the company primary email address
            var parms = '?UserId=' + _user_id;
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function(data, status, headers, config) {
                    $('#loadAccount').modal('hide');
                    console.log(data.Data[0]);
                    if (!data.Error){
                        var accountData = data.Data[0];
                        var authenticationString = accountData.username;
                        $scope.tbPrimaryEmail = authenticationString;
                        $scope.tbPhoneNumber = accountData.sip;
                        $scope.tbPhoneNumber2 = accountData.sip2;
                        $('#ddlCountryCode').val(accountData.sip_cc);
                        $('#ddlCountryCode2').val(accountData.sip2_cc);
                    }else{
                        window && console.log(data.ErrorDesc);
                    }
                });
        }

        $scope.editAccountSettings = function(){
            var email = $scope.tbPrimaryEmail;
            var countryCode = $('#ddlCountryCode').val();
            var phone = $scope.tbPhoneNumber;
            var countryCode2 = $('#ddlCountryCode2').val();
            var phone2 = $scope.tbPhoneNumber2;

            if (countryCode) countryCode = parseInt(countryCode);
            if (countryCode2) countryCode2 = parseInt(countryCode2);

            var emailValidation = validationServices.validateEmail(email, true);

            if (emailValidation){
                $scope.primaryEmailError = true;
                $scope.valPrimaryEmail= emailValidation;
            }else{
                $scope.valPrimaryEmail = "";
                $scope.primaryEmailError = false;
            }

            var countryCodeValidation = false;
            var phoneValidation = false;
            if (phone){
                countryCodeValidation = validationServices.validateRequiredField(countryCode, 'Select Country Code');
                phoneValidation = validationServices.validateRequiredInteger(phone, true, null, 'Invalid Phone Number');
                if (phoneValidation || countryCodeValidation){
                    $scope.phoneNumberError = true;
                    if (phoneValidation) $scope.valPhoneNumber = phoneValidation;
                    if (countryCodeValidation) $scope.valPhoneNumber = countryCodeValidation;
                }else{
                    $scope.valPhoneNumber = "";
                    $scope.phoneNumberError = false;
                }
            }

            var countryCode2Validation = false;
            var phone2Validation = false;
            if(phone2){
                var countryCode2Validation = validationServices.validateRequiredField(countryCode2, 'Select Country Code');
                var phone2Validation = validationServices.validateRequiredInteger(phone2, true, null, 'Invalid Phone Number');
                if (phone2Validation || countryCode2Validation){
                    $scope.phoneNumberError2 = true;
                    if (phone2Validation) $scope.valPhoneNumber2 = phone2Validation;
                    if (countryCode2Validation) $scope.valPhoneNumber2 = countryCode2Validation;
                }else{
                    $scope.valPhoneNumber2 = "";
                    $scope.phoneNumberError2 = false;
                }
            }

            if (
                emailValidation ||
                phoneValidation ||
                phone2Validation ||
                countryCode2Validation ||
                countryCodeValidation
            ){}else{
                $('#updateAccount').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('input').each(function(){
                    $(this).blur();
                });
                $('select').each(function(){
                    $(this).blur();
                });

                var parms = new Object();
                parms.UserId = _user_id;
                parms.Username = email;
                if (phone){
                    parms.SipCc  = countryCode;
                    parms.Sip = phone;
                }else{
                    parms.SipCc  = '';
                    parms.Sip = '';
                }
                if (phone2){
                    parms.Sip2Cc = countryCode2;
                    parms.Sip2 = phone2;
                }else{
                    parms.Sip2Cc = '';
                    parms.Sip2 = '';
                }

                console.log(parms);
                $http.post("/eyex-lite/controllers/users/updateUser.php", parms)
                    .success(function(data){
                        console.log(data);
                        if (!data.Error){
                            $scope.updateSuccess = true;
                            $scope.successMessage = 'Account details updated'
                            $scope.overallError = false;
                            $scope.valError = '';
                            $('#updateAccount').modal('hide');
                            getAccountDetails();
                        }else{
                            $scope.overallError = true;
                            $scope.valError = data.ErrorDesc;
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }

        }

        getAccountDetails();

    }]);
