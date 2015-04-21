'use strict';

/* Controllers */

angular.module('eyexApp.employeesController', []).
  controller('employeesController', ['$scope', '$http', 'countryServices', 'validationServices', 'eyexServices', function ($scope, $http, countryServices, validationServices, eyexServices) {


        $scope.employeeForm = true;

        var countries = countryServices.getCountries();
        for (var c = 0; c < countries.length; c++){
            var html = '<option value="' + countries[c].code + '">' + countries[c].name + ' (+' + countries[c].code + ')</option>'
            $('#ddlEditCountryCode').append(html);
            $('#ddlEditCountryCode2').append(html);
            $('#ddlCountryCode').append(html);
            $('#ddlCountryCode2').append(html);
        };

        var loadEmployees = function(){
            $('#loadEmployeesModal').modal({
                backdrop: 'static'
                , keyboard : false
            });
            var parms = '?Type=contact'
            $http.get("/eyex-lite/controllers/users/getUserDetails.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    console.log(data.Data);
                    $scope.employees = data.Data;
                    $('#loadEmployeesModal').modal('hide');
                });
        }

        var refreshAddEmployeeModal = function(){
            $scope.employeeForm = true;
            $scope.addingEmployee = false;
            $scope.employeeAdded = false;

            $scope.addEmployeeOverallError = false;
            $scope.firstNameError = false;
            $scope.lastNameError = false;
            $scope.emailError = false;
            $scope.pinError = false;
            $scope.phoneNumberError = false;
            $scope.phoneNumber2Error = false;
            $scope.cardIdError = false;
            $scope.passwordError = false;

            $scope.tbFirstName = '';
            $scope.tbLastName = '';
            $scope.tbEmail = '';
            $scope.tbPin = '';
            $scope.tbPhone = '';
            $scope.tbPhone2 = '';
            $scope.tbPassword = '';
            $scope.tbCardId = '';

            $('#ddlCountryCode').val('');
            $('#ddlCountryCode2').val('');
        }

        var refreshEditEmployeeModal = function(){
            $scope.editEmployeeForm = true;
            $scope.editingEmployeeDetail = false;
            $scope.confirmDeleteEmployee = false;
            $scope.deletingEmployee = false;
            $scope.archivingEmployee = false;

            $scope.editEmployeeOverallError = false;
            $scope.editFirstNameError = false;
            $scope.editLastNameError = false;
            $scope.editPinError = false;
            $scope.editCardIdError = false;
            $scope.editPasswordError = false;
            $scope.editUserTypeError = false;
            $scope.editPhoneNumberError = false;
            $scope.editPhoneNumber2Error = false;

            $scope.tbEditFirstName = '';
            $scope.tbEditLastName = '';
            $scope.tbEditPin = '';
            $scope.tbEditPhone = '';
            $scope.tbEditPhone2 = '';
            $scope.tbEditPassword = '';
            $('#ddlEditUserType').val(300);
            $('#ddlEditCountryCode').val('');
            $('#ddlEditCountryCode2').val('');
        }

        $scope.addEmployeePopup = function(){
            refreshAddEmployeeModal();
            $('#addEmployeeModal').modal({
                backdrop: 'static'
                , keyboard : false
            });
            $('#addEmployeeModal').on('shown.bs.modal', function(){
                $('#tbFirstName').focus();
            });

        }

        $scope.editEmployeePopup = function(userId){
            refreshEditEmployeeModal();
            $scope.editEmployeeForm = false;
            $scope.loadingEditEmployeeDetail = true;
            $("#tbEditFirstName").focus();
            $('#editEmployeeModal').modal({
                backdrop: 'static'
                , keyboard : false
            });
            $('#editEmployeeModal').on('shown.bs.modal', function(){
                $('#tbEditFirstName').focus();
            });
            //get the entity details here for populating field
            var parms = '?UserId=' + userId;
            parms += '&Type=contact'
            $http.get("/eyex-lite/controllers/users/getUserDetails.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    if (data.RowsReturned > 0){
                        var employeeData = data.Data[0];
                        $scope.loadingEditEmployeeDetail = false;
                        $scope.editEmployeeForm = true;

                        $('#ddlEditCountryCode').val(employeeData.sip_cc);
                        $('#ddlEditCountryCode2').val(employeeData.sip2_cc);
                        $scope.tbEditPhone = employeeData.sip;
                        $scope.tbEditPhone2 = employeeData.sip2;

                        $scope.tbEditFirstName = employeeData.first_name;
                        $scope.tbEditLastName = employeeData.last_name;
                        $scope.tbEditEmail = employeeData.username;
                        $scope.tbEditCardId = employeeData.card_id;

                        $('#ddlEditUserType').val(employeeData.authorization_level);

                        $scope.editUserId = userId;
                    }
                });
        }

        $scope.addEmployee = function(){
            var firstName = $scope.tbFirstName;
            var lastName = $scope.tbLastName;
            var email = $scope.tbEmail;
            var password = $scope.tbPassword;
            var pin = $scope.tbPin;
            var cardId = $scope.tbCardId;
            var countryCode = $('#ddlCountryCode').val();
            var mobile = $scope.tbPhone;
            var countryCode2 = $('#ddlCountryCode2').val();
            var mobile2 = $scope.tbPhone2;

            var firstNameValidation = validationServices.validateRequiredField(firstName);
            var lastNameValidation = validationServices.validateRequiredField(lastName);
            var emailValidation = validationServices.validateEmail(email, true);
            var pinValidation = validationServices.validateRequiredLength(pin, false, null, 4, 'PIN needs to be at least 4 characters long');
            var passwordValidation = validationServices.validateRequiredLength(password, true, null, 8, null);
            var pinIntegerValidation = validationServices.validateRequiredInteger(pin, false, null, 'PIN needs to be numeric');

            if (firstNameValidation){
                $scope.firstNameError = true;
                $scope.valFirstName = firstNameValidation;
            }else{
                $scope.valFirstName = "";
                $scope.firstNameError = false;
            }
            if (lastNameValidation){
                $scope.lastNameError = true;
                $scope.valLastName = lastNameValidation;
            }else{
                $scope.valLastName = "";
                $scope.lastNameError = false;
            }
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

            if (pinValidation || pinIntegerValidation){
                $scope.pinError = true;
                if (pinValidation){
                    $scope.valPin = pinValidation;
                }else if (pinIntegerValidation){
                    $scope.valPin = pinIntegerValidation;
                }
            }else{
                $scope.valPin = "";
                $scope.pinError = false;
            }

            var countryCodeValidation = false;
            var phoneValidation = false;
            if (mobile){
                countryCodeValidation = validationServices.validateRequiredField(countryCode, 'Select Country Code');
                phoneValidation = validationServices.validateRequiredInteger(mobile, true, null, 'Invalid Phone Number');
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
            if(mobile2){
                var countryCode2Validation = validationServices.validateRequiredField(countryCode2, 'Select Country Code');
                var phone2Validation = validationServices.validateRequiredInteger(mobile2, true, null, 'Invalid Phone Number');
                if (phone2Validation || countryCode2Validation){
                    $scope.phoneNumber2Error = true;
                    if (phone2Validation) $scope.valPhoneNumber2 = phone2Validation;
                    if (countryCode2Validation) $scope.valPhoneNumber2 = countryCode2Validation;
                }else{
                    $scope.valPhoneNumber2 = "";
                    $scope.phoneNumber2Error = false;
                }
            }

            if (
                    firstNameValidation ||
                    lastNameValidation ||
                    emailValidation ||
                    pinValidation ||
                    pinIntegerValidation ||
                    passwordValidation ||
                    phoneValidation ||
                    phone2Validation ||
                    countryCode2Validation ||
                    countryCodeValidation
            ){}else{
                var parms = new Object();
                parms.FirstName = firstName;
                parms.LastName = lastName;
                parms.Name = lastName + ' ' + firstName;
                parms.Status = 'A';
                parms.Username = email;
                parms.Password = password;
                parms.AuthorizationLevel = 300;
                parms.Type = 'contact';
                if (cardId) parms.CardId = cardId;
                if (pin) parms.PinNo = pin;
                if (mobile){
                    parms.Sip = mobile;
                    parms.SipCc = countryCode;
                }
                if (mobile2){
                    parms.Sip2 = mobile2;
                    parms.SipCc2 = countryCode2
                }
                $scope.employeeForm = false;
                $scope.addingEmployee = true;

                $http.post("/eyex-lite/controllers/users/addEmployee.php", parms)
                    .success(function(data){
                        console.log(data);
                        if (!data.Error){
                            $('#addEmployeeModal').modal('hide');
                            loadEmployees();
                        }else{
                            $scope.addingEmployee = false;
                            $scope.employeeForm = true;
                            if (data.ErrorDesc == 'PIN In Use'){
                                $scope.pinError = true;
                                $scope.valPin = 'PIN already taken';
                            }else if (data.ErrorDesc == 'Card ID In Use'){
                                $scope.cardIdError = true;
                                $scope.valCardId = 'Card ID already taken';
                            }else if (data.ErrorDesc == 'User Exists'){
                                $scope.emailError = true;
                                $scope.valEmail = 'User email taken';
                            }else{
                                $scope.addEmployeeOverallError = true;
                                $scope.valAddEmployee = data.ErrorDesc;
                                window && console.log(data.ErrorDesc);
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.editEmployee = function(){

            var firstName = $scope.tbEditFirstName;
            var lastName = $scope.tbEditLastName;
            var countryCode = $('#ddlEditCountryCode').val();
            var mobile = $scope.tbEditPhone;
            var countryCode2 = $('#ddlEditCountryCode2').val();
            var mobile2 = $scope.tbEditPhone2;
            var password = $scope.tbEditPassword;
            var pin = $scope.tbEditPin;
            var cardId = $scope.tbEditCardId;

            var firstNameValidation = validationServices.validateRequiredField(firstName);
            var lastNameValidation = validationServices.validateRequiredField(lastName);
            var pinValidation = validationServices.validateRequiredLength(pin, false, null, 4, 'PIN needs to be at least 4 characters long');
            var pinIntegerValidation = validationServices.validateRequiredInteger(pin, false, null, 'PIN needs to be numeric');
            var passwordValidation = validationServices.validateRequiredLength(password, false, null, 8, null);

            if (firstNameValidation){
                $scope.editFirstNameError = true;
                $scope.valEditFirstName = firstNameValidation;
            }else{
                $scope.valEditFirstName = "";
                $scope.editFirstNameError = false;
            }

            if (lastNameValidation){
                $scope.editLastNameError = true;
                $scope.valEditLastName = lastNameValidation;
            }else{
                $scope.valEditLastName = "";
                $scope.editLastNameError = false;
            }

            if (passwordValidation){
                $scope.editPasswordError = true;
                $scope.valEditPassword = passwordValidation;
            }else{
                $scope.valEditPassword = "";
                $scope.editPasswordError = false;
            }

            if (pinValidation || pinIntegerValidation){
                $scope.editPinError = true;
                if (pinValidation){
                    $scope.valEditPin = pinValidation;
                }else if (pinIntegerValidation){
                    $scope.valEditPin = pinIntegerValidation;
                }
            }else{
                $scope.valEditPin = "";
                $scope.editPinError = false;
            }

            var countryCodeValidation = false;
            var phoneValidation = false;
            if (mobile){
                countryCodeValidation = validationServices.validateRequiredField(countryCode, 'Select Country Code');
                phoneValidation = validationServices.validateRequiredInteger(mobile, true, null, 'Invalid Phone Number');
                if (phoneValidation || countryCodeValidation){
                    $scope.editPhoneNumberError = true;
                    if (phoneValidation) $scope.valEditPhoneNumber = phoneValidation;
                    if (countryCodeValidation) $scope.valEditPhoneNumber = countryCodeValidation;
                }else{
                    $scope.valEditPhoneNumber = "";
                    $scope.editPhoneNumberError = false;
                }
            }

            var countryCode2Validation = false;
            var phone2Validation = false;
            if(mobile2){
                var countryCode2Validation = validationServices.validateRequiredField(countryCode2, 'Select Country Code');
                var phone2Validation = validationServices.validateRequiredInteger(mobile2, true, null, 'Invalid Phone Number');
                if (phone2Validation || countryCode2Validation){
                    $scope.editPhoneNumber2Error = true;
                    if (phone2Validation) $scope.valEditPhoneNumber2 = phone2Validation;
                    if (countryCode2Validation) $scope.valEditPhoneNumber2 = countryCode2Validation;
                }else{
                    $scope.valEditPhoneNumber2 = "";
                    $scope.editPhoneNumber2Error = false;
                }
            }

            if (
                firstNameValidation ||
                lastNameValidation ||
                phoneValidation ||
                phone2Validation ||
                countryCode2Validation ||
                countryCodeValidation ||
                passwordValidation||
                pinValidation ||
                pinIntegerValidation
            ){}else{
                $scope.editingEmployeeDetail = true;
                $scope.editEmployeeForm = false;

                var parmsEdit = new Object();
                parmsEdit.UserId = $scope.editUserId;
                parmsEdit.FirstName = firstName;
                parmsEdit.LastName = lastName;
                parmsEdit.Name = lastName + ' ' + firstName;
                if (password){
                    parmsEdit.Password = password;
                }
                if (pin){
                    parmsEdit.PinNo = pin;
                }
                if (cardId){
                    parmsEdit.CardId = cardId;
                }
                if (mobile){
                    parmsEdit.Sip = mobile;
                    parmsEdit.SipCc = countryCode;
                }else{
                    parmsEdit.Sip = '';
                    parmsEdit.SipCc = '';
                }
                if (mobile2){
                    parmsEdit.Sip2 = mobile2;
                    parmsEdit.SipCc2 = countryCode2;
                }else{
                    parmsEdit.Sip2 = '';
                    parmsEdit.SipCc2 = '';
                }

                $http.post("/eyex-lite/controllers/users/updateEmployee.php", parmsEdit)
                    .success(function(data){
                        console.log(data);
                        if (!data.Error){
                            $scope.valEditEmployee = '';
                            $scope.editEmployeeOverallError = false;
                            $scope.editingEmployeeDetail = false;
                            $("#editEmployeeModal").modal('hide');
                            loadEmployees();
                        }else{
                            $scope.editingEmployeeDetail = false;
                            $scope.editEmployeeForm = true;
                            if (data.ErrorDesc == 'PIN In Use'){
                                $scope.editPinError = true;
                                $scope.valEditPin = 'PIN already taken';
                            }else if (data.ErrorDesc == 'You are using this PIN'){
                                $scope.editPinError = true;
                                $scope.valEditPin = data.ErrorDesc;
                            }else if (data.ErrorDesc == 'Card ID In Use'){
                                $scope.editCardIdError = true;
                                $scope.valEditCardId = 'Card ID already taken';
                            }else{
                                $scope.editEmployeeOverallError = true;
                                $scope.valEditEmployee = data.ErrorDesc;
                            }
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.deleteEmployee = function(){
            $scope.editEmployeeForm = false;
            $scope.confirmDeleteEmployee = true;
        }

        $scope.cancelDeleteEmployee = function(){
            $scope.editEmployeeForm = true;
            $scope.confirmDeleteEmployee = false;
        }

        $scope.deleteEmployeeConfirmed = function(){
            var parms = new Object();
            parms.EmployeeId = $scope.editUserId;
            $scope.confirmDeleteEmployee = false;
            $scope.deletingEmployee = true;
            $http.post("/eyex-lite/controllers/users/deleteEmployee.php", parms)
                .success(function(data){
                    console.log(data);
                    $scope.deletingEmployee = false;
                    $('#editEmployeeModal').modal('hide');
                    loadEmployees();
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.archiveEmployeeConfirmed = function(){
            var parms = new Object();
            parms.EmployeeId = $scope.editUserId;
            $scope.confirmDeleteEmployee = false;
            $scope.archivingEmployee = true;
            $http.post("/eyex-lite/controllers/users/archiveEmployee.php", parms)
                .success(function(data){
                    console.log(data);
                    $scope.archivingEmployee = false;
                    $('#editEmployeeModal').modal('hide');
                    loadEmployees();
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.cancelAddEmployee = function(){
            refreshAddEmployeeModal();
            $('#addEmployeeModal').modal('hide');
        }

        $scope.cancelEditEmployee = function(){
            refreshEditEmployeeModal();
            $('#editEmployeeModal').modal('hide');
        }

        $scope.syncEmployees = function(){
            $('#syncingEmployees').modal({
                backdrop: 'static'
                , keyboard: false
            })
            eyexServices.deviceBroadcast(function(data){
                $('#syncingEmployees').modal('hide');
                console.log(data);
            })
        }

        $scope.editAuthorizationPopup = function(employeeId, employeeName){
            $scope.editEmployeeAuthorizationForm = true;
            $("#editAuthorizationModal").modal({
                backdrop: 'static'
                , keyboard : false
            });
            $scope.authorizationName = employeeName + "'s";
            //get the authorizations
            var parms = '?EmployeeId=' + employeeId;
            $http.get("/eyex-lite/controllers/device/getDeviceEmployeeAuthentication.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    console.log(data.Data);
                    $scope.employeeDeviceAuthentications = data.Data;
                    $scope.editAuthorizationEmployeeId = employeeId;
                });
        }

        $scope.editAuthorization = function(){
            $scope.editEmployeeAuthorizationForm = false;
            $scope.editingAuthorization = true;
            //assign the authorization
            var authorizedArray = new Array();
            $('.device-authorized').each(function(){
                if ($(this).is(':checked')){
                    var deviceId = $(this).val();
                    authorizedArray.push(deviceId);
                }
            });
            var deviceIdArray = JSON.stringify(authorizedArray);
            var parms = new Object();
            parms.EmployeeId = $scope.editAuthorizationEmployeeId;
            parms.DeviceIds = deviceIdArray;
            $http.post("/eyex-lite/controllers/deviceRelationship/assignEmployeeDeviceAuth.php", parms)
                .success(function(data){
                    $scope.editingAuthorization = false;
                    if (!data.Error){
                        $("#editAuthorizationModal").modal('hide');
                    }else{
                        window && console.log(data);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.cancelEditAuthorizationLevel = function(){
            $("#editAuthorizationModal").modal('hide');
        }
        loadEmployees();
  }]);
