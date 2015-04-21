'use strict';

/* Controllers */

angular.module('eyexApp.editProfileController', []).
  controller('editProfileController', ['$scope', '$http', '$rootScope', 'validationServices', function ($scope, $http, $rootScope, validationServices) {
        $("#uploadProfilePicForm").attr('action', '/eyex-lite/controllers/employees/uploadUserProfilePicture.php?UserId=' + _user_id);
        // Check to see when a user has selected a file
        $('#profilePicUpload').on('change', function() {
            if ($('#profilePicUpload').val()){
                uploadProfilePicture();
            }
        });

        $('#tbFirstName').focus();

        var loadProfile = function() {
            $('#loadingProfile').modal({
                backdrop: 'static'
                , keyboard : false
            });

            var parms = "?UserId=" + _user_id;
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function (data, status, headers, config) {
                    $('#loadingProfile').modal('hide');
                    console.log(data.Data);
                    var employeeData = data.Data[0];
                    $scope.tbFirstName = employeeData.first_name;
                    $scope.tbLastName = employeeData.last_name;

                    if (employeeData.apartment) $scope.tbBlk = employeeData.apartment;
                    if (employeeData.street_1) $scope.tbRoad = employeeData.street_1;
                    if (employeeData.suite){
                        var unit1 = (employeeData.suite).substring(0, (employeeData.suite).indexOf("-"));
                        var unit2 = (employeeData.suite).substring((employeeData.suite).indexOf("-") + 1);
                        $scope.tbUnit1 = unit1;
                        $scope.tbUnit2 = unit2;
                    }
                    if (employeeData.postcode){
                        $scope.tbZip = employeeData.postcode;
                    }
                    if (employeeData.country){
                        $('#ddlCountry').val(employeeData.country);
                    }
                    if (_user_img){
                        $("#showNoProfilePicture").hide();
                        $("#showProfilePicture").show();
                        $("#imgProfilePicture").prop('src', _user_img);
                        $('#removeProfilePic').css('display', 'inline-block');
                    }
                });
        };

        $scope.editProfile = function(){
            var firstName = $scope.tbFirstName;
            var lastName = $scope.tbLastName;
            var country = $("#ddlCountry").val();
            var block = '';
            var roadName = '';
            var unit = '';
            var zip = '';
            if ($scope.tbBlk) block = $scope.tbBlk;
            if ($scope.tbRoad) roadName = $scope.tbRoad;
            if ($scope.tbUnit1 && $scope.tbUnit2) unit = $scope.tbUnit1 + '-' + $scope.tbUnit2;
            if ($scope.tbZip) zip = $scope.tbZip;

            var validateFirstName = validationServices.validateRequiredField(firstName);
            var validateLastName = validationServices.validateRequiredField(lastName);
            
            if (validateFirstName){
                $scope.firstNameError = true;
                $scope.valFirstName = validateFirstName;
            }else{
                $scope.valFirstName = '';
                $scope.firstNameError = false;
            }
            
            if (validateLastName){
                $scope.lastNameError = true;
                $scope.valLastName = validateLastName;
            }else{
                $scope.valLastName = '';
                $scope.lastNameError = false;
            }

            if (
                validateFirstName &&
                validateLastName
            ){
                return;
            }else {
                $('#updatingProfile').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                var parms = new Object();
                parms.UserId = _user_id;
                parms.FirstName = firstName;
                parms.LastName = lastName;
                parms.Apartment = block;
                parms.RoadName = roadName;
                parms.Country = country;
                parms.Suite = unit;
                parms.Zip = zip;
                console.log(parms);

                $http.post("/eyex-lite/controllers/users/updateUser.php", parms)
                    .success(function(data){
                        $('#updatingProfile').modal('hide');
                        loadProfile();
                        if (!data.Error){
                            window && console.log(data);
                        }else{
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        };

        var uploadProfilePicture = function(){
            $("#profilePicError").hide();
            $("#showProfilePicture").hide();
            $("#showNoProfilePicture").hide();
            $("#showUploadingProfilePicture").show();
            $('#removeProfilePic').hide();
            $('#uploadProfilePicForm').ajaxSubmit({
                error: function(xhr) {
                    $("#showUploadingProfilePicture").hide();
                    if ($scope.logoId){
                        $("#showProfilePicture").show();
                    }else{
                        $("#showNoProfilePicture").show();
                    }
                },
                success: function(response) {
                    console.log(response);
                    var resData = JSON.parse(response);
                    if (!resData.Error){
                        var imgSrc = '../../../data/' + _user_id + '/' + resData.Data[0].file_name; //image src
                        _user_img = imgSrc;
                        $("#showUploadingProfilePicture").hide();
                        $("#showProfilePicture").show();
                        $("#imgProfilePicture").attr('src', imgSrc);
                        //reset the form
                        $('#uploadProfilePicForm').get(0).reset();
                        $('#removeProfilePic').css('display', 'inline-block');
                    }else{
                        $("#profilePicError").text(resData.ErrorDesc);
                        $("#profilePicError").show();
                        $("#showUploadingProfilePicture").hide();
                        $('#removeProfilePic').css('display', 'inline-block');
                        if (_user_img){
                            $("#showProfilePicture").show();
                        }else{
                            $("#showNoProfilePicture").show();
                        }
                    }
                }
            });
            return false;
        }


        $scope.removeProfilePicture = function(){
            $("#profilePicError").hide();
            $('#removeProfilePic').hide();
            $("#showUploadingProfilePicture").show();
            $("#showProfilePicture").hide();
            $("#imgProfilePicture").attr('src', '');
            var parms = new Object();
            parms.UserId = _user_id;
            $http.post("/eyex-lite/controllers/employees/removeUserProfilePicture.php", parms)
                .success(function(data){
                    if (!data.Error){
                        $("#showUploadingProfilePicture").hide();
                        $("#showNoProfilePicture").show();
                        //reset the form
                        $('#uploadProfilePicForm').get(0).reset();
                        //reset the upload field with no logo ID
                        $("#uploadProfilePicForm").attr('action', '/eyex-lite/controllers/employees/uploadUserProfilePicture.php?UserId=' + _user_id);
                        window && console.log(data);
                    }else{
                        window && console.log(data.ErrorDesc);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        loadProfile();

  }]);

