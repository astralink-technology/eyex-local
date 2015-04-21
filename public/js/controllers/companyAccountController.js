    'use strict';

/* Controllers */

angular.module('eyexApp.companyAccountController', []).
  controller('companyAccountController', ['$scope', '$http', 'countryServices', 'validationServices', function ($scope, $http, countryServices, validationServices) {

        var countries = countryServices.getCountries();
        for (var c = 0; c < countries.length; c++){
            var html = '<option value="' + countries[c].code + '">' + countries[c].name + ' (+' + countries[c].code + ')</option>'
            $('#ddlCountryCode').append(html);
        };

        var getCompanyAccountDetails = function(){
            $('#loadCompanyAccount').modal({
                backdrop: 'static',
                keyboard: false
            })
            var parms = '?UserId=' + _user_id;
            //get the company primary email address
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function(data, status, headers, config) {
                    if (data.RowsReturned > 0){
                        $('#loadCompanyAccount').modal('hide');
                        var companyAccountData = data.Data[0];
                        var companyId = companyAccountData.id;
                        var authenticationString = companyAccountData.username;
                        var sip = companyAccountData.sip;
                        var sipCc = companyAccountData.sip_cc;
                        $scope.tbCompanyEmail = authenticationString;
                        $scope.tbCompanyPhone = sip;
                        $scope.companyId = companyId;
                        $("#ddlCountryCode").val(sipCc);
                    }
                });
        }

        $scope.editCompanyAccountSettings = function(){

            var email = $scope.tbCompanyEmail;
            var countryCode = $('#ddlCountryCode').val();
            var companyPhone = $scope.tbCompanyPhone;

            var emailValidation = validationServices.validateEmail(email, true);
            if (emailValidation){
                $scope.companyEmailError = true;
                $scope.valCompanyEmail = emailValidation;
            }else{
                $scope.valCompanyEmail = "";
                $scope.companyEmailError = false;
            }


            var countryCodeValidation = false;
            var phoneValidation = false;
            if (companyPhone){
                countryCodeValidation = validationServices.validateRequiredField(countryCode, 'Select Country Code');
                phoneValidation = validationServices.validateRequiredInteger(companyPhone, true, null, 'Invalid Phone Number');
                if (phoneValidation || countryCodeValidation){
                    $scope.companyPhoneError = true;
                    if (phoneValidation) $scope.valCompanyPhone = phoneValidation;
                    if (countryCodeValidation) $scope.valCompanyPhone = countryCodeValidation;
                }else{
                    $scope.valCompanyPhone = "";
                    $scope.companyPhoneError = false;
                }
            }

            if (
                emailValidation ||
                phoneValidation ||
                countryCodeValidation)
            {}else{
                $('#updatingCompanyAccount').modal({
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
                if (companyPhone){
                    parms.Sip = companyPhone;
                    parms.SipCc = countryCode;
                }else{
                    parms.Sip = '';
                    parms.SipCc = '';
                }

                //update company account details
                $http.post("/eyex-lite/controllers/users/updateUser.php", parms)
                    .success(function(data){
                        if (!data.Error){
                            $scope.updateSuccess = true;
                            $scope.successMessage = 'Company account details updated'
                            $scope.overallError = false;
                            $scope.valError = '';
                            $('#updatingCompanyAccount').modal('hide');
                            getCompanyAccountDetails();
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

        getCompanyAccountDetails();
  }]);
