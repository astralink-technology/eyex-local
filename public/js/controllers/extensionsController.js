'use strict';

/* Controllers */

angular.module('eyexApp.extensionsController', []).
  controller('extensionsController', ['$scope', '$http', '$rootScope', 'validationServices', 'eyexServices', function ($scope, $http, $rootScope, validationServices, eyexServices) {
        //get the list of employees that has no extensions
        var _notAssigned = new Array();
        var _extensionList = new Array();
        var getNonAssigned = function(){
            $('#ddlAddAssignTo').html('<option value="">Select Employee</option>');
            $('#ddlEditAssignedTo').html('<option value="">Select Employee</option>');
            var parms = '?Type=contact';
            $http.get("/eyex-lite/controllers/users/getUserDetails.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    for (var i = 0; i < data.RowsReturned; i ++){
                        if (!data.Data[i].extension_number){
                            var employee = new Object();
                            var optionHtml = '<option value="' + data.Data[i].id +'">' + data.Data[i].name +'</option>';
                            employee.name = data.Data[i].name;
                            employee.entity_id = data.Data[i].id;
                            _notAssigned.push(employee);
                            $('#ddlEditAssignedTo').append(optionHtml);
                            $('#ddlAddAssignTo').append(optionHtml);
                        }
                    }
                    $('#ddlAddAynsignTo').val('');
                    $('#ddlEditAssignedTo').val('');
                });
        }

        var refreshAddExtensionForm = function(){
            $("#addExtensionModal").modal('hide');
            $scope.addingExtension = false;
            $scope.addExtensionForm = false;
            $scope.tbExtensionNumber = '';
            $scope.tbExtensionPassword = '';
            $scope.tbConfirmExtensionPassword = '';
            $scope.extensionNumberError = false;
            $scope.extensionPasswordError = false;
            $scope.extensionConfirmPasswordError = false;
            getNonAssigned();
        }

        var refreshEditExtensionForm = function(){
            $("#editExtensionModal").modal('hide');
            $scope.editExtensionForm = false;
            $scope.deletingExtension = false;
            $scope.deleteExtensionConfirmForm = false;
            $scope.editingExtension = false;
            getNonAssigned();
        }

        var getExtensions = function(){
            $('#loadExtensions').modal({
                backdrop: 'static'
                , keyboard: false
            })
            $http.get("/eyex-lite/controllers/extensions/getExtensionDetail.php")
            .success(function(data, status, headers, config)
            {
                $('#loadExtensions').modal('hide');
                $scope.extensions = data.Data;
                _extensionList = new Array();
                for (var i = 0; i < data.RowsReturned; i ++){
                    _extensionList.push(data.Data[i].extension_number);
                }
            });
        }

        $scope.addExtensionPopup = function(){
            $('#addExtensionModal').modal({
                backdrop : 'static',
                keyboard : false
            });
            $scope.addExtensionForm = true;
        }

        $scope.editExtensionPopup = function(extensionNumber, userId, id, name, profilePic){
            $('#editExtensionModal').modal({
                backdrop : 'static',
                keyboard : false
            });
            $scope.editExtensionForm = true;
            $scope.editExtensionId = id;
            $scope.editExtensionNumber = extensionNumber;
            $scope.editAssignedId = userId;
            $scope.editExtensionProfilePic = profilePic;
            if (userId){
                $scope.assignedExt = true;
                $scope.notAssignedExt = false;
                if (profilePic){
                    $("#editExtImgUrl").removeAttr('src');
                    $("#editExtImgUrl").css('background-image', 'url(' + profilePic + ')');
                }else{
                    $("#editExtImgUrl").prop('src', '/eyex-lite/public/img/avartar-xs.png');
                }
                $('#ddlEditAssignedTo').append('<option value="' + userId + '">' + name + '</option>');
            }else{
                $scope.notAssignedExt = true;
                $scope.assignedExt = false;
            }
        }

        $scope.syncExtensions = function(){
            $('#syncingExtensions').modal({
                backdrop: 'static'
                , keyboard: false
            })
            eyexServices.deviceBroadcast(function(){
                $('#syncingExtensions').modal('hide');
            })
        }

        $scope.editExtension = function(){
            var newAssignedId = $('#ddlEditAssignedTo').val();
            var parms = new Object();
            $scope.editExtensionForm = false;
            $scope.editingExtension = true;
            parms.ExtensionId = $scope.editExtensionId;
            parms.UserId = newAssignedId;
            console.log(parms);
            $http.post("/eyex-lite/controllers/extensions/updateExtension.php", parms)
                .success(function(data){
                    console.log(data);
                    if (!data.Error){
                        refreshEditExtensionForm();
                        getExtensions();
                    }else{
                        $scope.editingExtension = false;
                        $scope.editExtensionForm = true;
                        window && console.log(data.ErrorDesc);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });

        }

        $scope.addExtension = function(){
            var extensionNumber = $scope.tbExtensionNumber;
            var extensionPassword = $scope.tbExtensionPassword;
            var extensionConfirmPassword = $scope.tbConfirmExtensionPassword;
            var voicemailPassword = $scope.tbVoicemailPassword;

            var assignId = $('#ddlAddAssignTo').val();

            var extensionNumberValidation = validationServices.validateRequiredInteger(extensionNumber, true, null, 'Extension number needs to be numeric');
            var extensionPasswordValidation = validationServices.validateRequiredLength(extensionPassword, true, null, 6, null);
            var extensionConfirmPasswordValidation = validationServices.validateRequiredSimilarField(extensionConfirmPassword, true, null, extensionPassword, 'Passwords does not match');

            if (extensionNumberValidation || _extensionList.indexOf(parseInt(extensionNumber)) >= 0){
                if (extensionNumberValidation){
                    $scope.extensionNumberError = true;
                    $scope.valExtensionNumber = extensionNumberValidation;
                }
                if (_extensionList.indexOf(parseInt(extensionNumber)) >= 0){
                    $scope.extensionNumberError = true;
                    $scope.valExtensionNumber = 'Extension is already added';
                }
            }else{
                $scope.valExtensionNumber = "";
                $scope.extensionNumberError = false;
            }

            if (extensionPasswordValidation){
                $scope.extensionPasswordError = true;
                $scope.valExtensionPassword = extensionPasswordValidation;
            }else{
                $scope.valExtensionPassword = "";
                $scope.extensionPasswordError = false;
            }

            if (extensionConfirmPasswordValidation){
                $scope.extensionConfirmPasswordError = true;
                $scope.valExtensionConfirmPassword = extensionConfirmPasswordValidation;
            }else{
                $scope.valExtensionConfirmPassword = "";
                $scope.extensionConfirmPasswordError = false;
            }

            if (
                extensionNumberValidation ||
                extensionPasswordValidation ||
                extensionConfirmPasswordValidation
                //_extensionList.indexOf(parseInt(extensionNumber)) >= 0
            ){}else{
                var parms = new Object();
                parms.ExtensionNumber = extensionNumber;
                parms.Password = extensionPassword;
                if (voicemailPassword){
                    parms.VoicemailPassword = voicemailPassword;
                }
                if (assignId) parms.UserId = assignId;

                $scope.addExtensionForm = false;
                $scope.addingExtension = true;

                $http.post("/eyex-lite/controllers/extensions/addExtension.php", parms)
                    .success(function(data){
                        if (!data.Error){
                            $scope.valAddExtension = '';
                            $scope.overallError = false;
                            refreshAddExtensionForm();
                            getExtensions();
                        }else{
                            $scope.addingExtension = false;
                            $scope.addExtensionForm = true;
                            $scope.overallError = true;
                            $scope.valAddExtension = data.ErrorDesc;
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.deleteExtensionConfirm = function(){
            $scope.editExtensionForm = false;
            $scope.deleteExtensionConfirmForm = true;
            if ($scope.editExtensionAssignedId){
                $scope.ifConfirmDelete = true;
                $scope.ifNotAssignedConfirmDelete = false;
            }else{
                $scope.ifNotAssignedConfirmDelete = true;
                $scope.ifConfirmDelete = false;
            }
        }

        $scope.cancelDeleteExtension = function(){
            $scope.deleteExtensionConfirmForm = false;
            $scope.editExtensionForm = true;
        }

        $scope.deleteExtension = function(){
            $scope.deletingExtension = true;
            $scope.deleteExtensionConfirmForm = false;
            var parms = new Object();
            parms.ExtensionId = $scope.editExtensionId;
            $http.post("/eyex-lite/controllers/extensions/deleteExtension.php", parms)
                .success(function(data){
                    console.log(data);
                    if (!data.Error){
                        refreshEditExtensionForm();
                        getExtensions();
                    }else{
                        window && console.log(data.ErrorDesc);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.cancelAddExtension = function(){
            $('#addExtensionModal').modal('hide');
            refreshAddExtensionForm();
        }

        $scope.cancelEditExtension = function(){
            $('#editExtensionModal').modal('hide');
            refreshEditExtensionForm();
        }

        getNonAssigned();
        getExtensions();
  }]);
