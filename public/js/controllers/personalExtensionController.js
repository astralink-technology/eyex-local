'use strict';

/* Controllers */

angular.module('eyexApp.personalExtensionController', []).
  controller('personalExtensionController', ['$scope', '$http', '$rootScope', 'validationServices', 'eyexServices'
        , function ($scope, $http, $rootScope, validationServices, eyexServices) {

            var loadExtension = function(){
                $('#loadExtension').modal({
                    backdrop: 'static',
                    keyboard: false
                })

                var extParms = '?UserId=' + _user_id;
                $http.get("/eyex-lite/controllers/extensions/getExtensions.php" + encodeURI(extParms))
                    .success(function(data, status, headers, config)
                    {
                        $('#loadExtension').modal('hide');
                        console.log(data.Data);
                        if (!data.Error && data.RowsReturned > 0){
                            var extData = data.Data[0];
                            $scope.tbExtensionNumber = extData.extension_number;
                            $scope.extensionId = extData.id;
                        }else{
                            window && console.log(data.ErrorDesc);
                        }
                    });
            }


            var refreshExtensionForm = function(){
                $scope.tbVoicemailPassword = '';
                $scope.voicemailPasswordError = false;
            }

            loadExtension();
            $("#tbVoicemailPassword").focus();

            $scope.editExtension = function(){
                var voicemailPassword = $scope.tbVoicemailPassword;

                var voicemailValidation = validationServices.validateRequiredLength(voicemailPassword, true, null, 6);

                if (voicemailValidation){
                    $scope.voicemailPasswordError = true;
                    $scope.valVoicemailPassword = voicemailValidation;
                }else{
                    $scope.voicemailPasswordError = "";
                    $scope.valVoicemailPassword = false;
                }

                if (
                    voicemailValidation
                ){}else{
                    $("#editingExtension").modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                    $('input').blur();
                    //update sip settings
                    var parms= new Object();
                    parms.ExtensionId = $scope.extensionId;
                    parms.VoicemailPassword = voicemailPassword;
                    console.log(parms);
                    $http.post("/eyex-lite/controllers/extensions/updateExtension.php", parms)
                        .success(function(data){
                            $("#editingExtension").modal('hide');
                            console.log(data);
                            if (!data.Error){
                                $scope.updateSuccess = true;
                                $scope.successMessage = 'Extension details updated'
                                $scope.overallError = false;
                                $scope.valError = '';
                                refreshExtensionForm();
                            }else{
                                window && console.log(data.ErrorDesc);
                            }
                        })
                        .error(function(data) {
                            window && console.log(data);
                        });
                }
            }
  }]);
