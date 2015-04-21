'use strict';

/* Controllers */

angular.module('eyexApp.editCompanyProfileController', []).
    controller('editCompanyProfileController', ['$scope', '$http', '$rootScope', 'validationServices', function ($scope, $http, $rootScope, validationServices) {

        $("#uploadCompanyLogoForm").attr('action', '/eyex-lite/controllers/company/uploadCompanyLogo.php');
        // Check to see when a user has selected a file
        $('#coLogoUpload').on('change', function() {
            if ($('#coLogoUpload').val()){
                uploadCompanyLogo();
            }
        });

        $scope.workStart = moment({ hour: 8});
        $scope.workEnd = moment({ hour:17});

        var loadEditCompanyProfile = function(){
            $('#loadCompanyProfile').modal({
                backdrop: 'static',
                keyboard: false
            })
            var parms = '?AuthorizationLevel=400';
            $http.get("/eyex-lite/controllers/users/getUserDetails.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    $('#loadCompanyProfile').modal('hide');
                    console.log(data.Data);
                    if (!data.Error){
                        var companyData = data.Data[0];
                        $scope.companyId = companyData.id;
                        $scope.tbCompanyName = companyData.name;
                        if (companyData.apartment) $scope.tbBlk = companyData.apartment;
                        if (companyData.street_1) $scope.tbRoad = companyData.street_1;
                        if (companyData.suite){
                            var unit1 = (companyData.suite).substring(0, (companyData.suite).indexOf("-"));
                            var unit2 = (companyData.suite).substring((companyData.suite).indexOf("-") + 1);
                            $scope.tbUnit1 = unit1;
                            $scope.tbUnit2 = unit2;
                        }
                        if (companyData.postcode){
                            $scope.tbZip = companyData.postcode;
                        }
                        if (companyData.country){
                            $('#ddlCountry').val(companyData.country);
                        }
                        if (companyData.extra_data2 && companyData.extra_data3){
                            $('#ddlWebsiteProtocol').val(companyData.extra_data2);
                            $scope.tbWebsite = companyData.extra_data3;
                        }
                        if (companyData.extra_data){
                            var workDaysArray = (companyData.extra_data).split(',');
                            console.log(workDaysArray);
                            for (var i = 0; i < workDaysArray.length; i++){
                                if (workDaysArray[i] == '1'){
                                    $("#cbMon").prop('checked', "checked");
                                }else if (workDaysArray[i] == '2'){
                                    $("#cbTue").prop('checked', "checked");
                                }else if (workDaysArray[i] == '3'){
                                    $("#cbWed").prop('checked', "checked");
                                }else if (workDaysArray[i] == '4'){
                                    $("#cbThu").prop('checked', "checked");
                                }else if (workDaysArray[i] == '5'){
                                    $("#cbFri").prop('checked', "checked");
                                }else if (workDaysArray[i] == '6'){
                                    $("#cbSat").prop('checked', "checked");
                                }else if (workDaysArray[i] == '7'){
                                    $("#cbSun").prop('checked', "checked");
                                }
                            }
                        }
                        if (companyData.extra_date_time != '0000-00-00 00:00:00'){
                            $scope.workStart = moment(moment(moment(companyData.extra_date_time).format('YYYY-MM-DDTHH:mm:ss+0000')).format('LLLL'));
                        }
                        if (companyData.extra_date_time2 != '0000-00-00 00:00:00'){
                            $scope.workEnd = moment(moment(moment(companyData.extra_date_time2).format('YYYY-MM-DDTHH:mm:ss+0000')).format('LLLL'));
                        }
                        if (_user_img){
                            $("#showNoCompanyLogo").hide();
                            $("#showCompanyLogo").show();
                            $("#imgCoLogo").prop('src', _user_img);
                            $('#removeCompanyLogo').css('display', 'inline-block');
                        }
                    }else{
                        window && console.log(data.ErrorDesc);
                    }
                });
        }

        $scope.saveCompanyDetails = function(){
            //get the details
            var companyName = $scope.tbCompanyName;
            var companyWebsite = $scope.tbWebsite;
            var country = $("#ddlCountry").val();
            var protocol = $("#ddlWebsiteProtocol").val();
            var block = '';
            var roadName = '';
            var unit = '';
            var zip = '';
            if ($scope.tbBlk) block = $scope.tbBlk;
            if ($scope.tbRoad) roadName = $scope.tbRoad;
            if ($scope.tbUnit1 && $scope.tbUnit2) unit = $scope.tbUnit1 + '-' + $scope.tbUnit2;
            if ($scope.tbZip) zip = $scope.tbZip;
            var validateCompanyName = validationServices.validateRequiredField(companyName);
            if (validateCompanyName){
                $scope.companyNameError = true;
                $scope.valCompanyName = validateCompanyName;
            }else{
                $scope.valCompanyName = '';
                $scope.companyNameError = false;
            }
            var validateWebsiteProtocol = false;
            var validateWebsiteUrl = false;
            if (companyWebsite){
                validateWebsiteProtocol =  validationServices.validateRequiredField(protocol, 'Select Protocol');
                validateWebsiteUrl =  validationServices.validateRequiredUrl(companyWebsite, false);
            }

            if (validateWebsiteUrl || validateWebsiteProtocol){
                $scope.websiteError = true;
                if (validateWebsiteUrl) $scope.valWebsite = validateWebsiteUrl;
                if (validateWebsiteProtocol) $scope.valWebsite = validateWebsiteProtocol;
            }else{
                $scope.valWebsite = '';
                $scope.websiteError = false;
            }

            if (
                validateCompanyName ||
                validateWebsiteProtocol ||
                validateWebsiteUrl
            ){
              return;
            }else{
                $('#updateCompanyProfile').modal({
                    backdrop: 'static',
                    keyboard: false
                });

                //working hours
                var workStart = $scope.workStart;
                var workEnd = $scope.workEnd;

                //working days
                var workingDays = '';
                if($("#cbMon").is(":checked")) workingDays += '1,';
                if($("#cbTue").is(":checked")) workingDays += '2,';
                if($("#cbWed").is(":checked")) workingDays += '3,';
                if($("#cbThu").is(":checked")) workingDays += '4,';
                if($("#cbFri").is(":checked")) workingDays += '5,';
                if($("#cbSat").is(":checked")) workingDays += '6,';
                if($("#cbSun").is(":checked")) workingDays += '7,';

                //remove the last ,
                var workingDaysLastIndex = workingDays.lastIndexOf(',');
                var newWorkingDays = workingDays.slice(0, workingDaysLastIndex);

                var parms = new Object();
                parms.Name = companyName;
                parms.UserId = $scope.companyId;
                parms.Apartment = block;
                parms.Street1 = roadName;
                parms.Suite = unit;
                parms.Postcode = zip;
                parms.Country = country;
                parms.ExtraData = newWorkingDays;
                parms.ExtraDateTime = workStart;
                parms.ExtraDateTime2 = workEnd;
                parms.ExtraData2 = protocol;
                parms.ExtraData3 = companyWebsite;
                console.log(parms);
                $http.post("/eyex-lite/controllers/users/updateUser.php", parms)
                    .success(function(data){
                        $('#updateCompanyProfile').modal('hide');
                        if (!data.Error){
                            window && console.log(data);
                            $('#updateCompanyProfile').modal('hide');
                            loadEditCompanyProfile();
                        }else{
                            window && console.log(data.ErrorDesc);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });

            }
        }

        $scope.removeCompanyLogo = function(){
            $("#companyLogoError").hide();
            $('#removeCompanyLogo').hide();
            $("#showUploadingCompanyLogo").show();
            $("#showCompanyLogo").hide();
            $("#imgCoLogo").attr('src', '');
            $http.post("/eyex-lite/controllers/company/removeCompanyLogo.php")
                .success(function(data){
                    if (!data.Error){
                        $("#showUploadingCompanyLogo").hide();
                        $("#showNoCompanyLogo").show();
                        //reset the form
                        $('#uploadCompanyLogoForm').get(0).reset();
                        $scope.logoId = '';
                        window && console.log(data);
                    }else{
                        window && console.log(data.ErrorDesc);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        var uploadCompanyLogo = function(){
            $("#companyLogoError").hide();
            $("#showCompanyLogo").hide();
            $("#showNoCompanyLogo").hide();
            $("#showUploadingCompanyLogo").show();
            $('#removeCompanyLogo').hide();
            $('#uploadCompanyLogoForm').ajaxSubmit({
                error: function(xhr) {
                    $("#showUploadingCompanyLogo").hide();
                    if ($scope.logoId){
                        $("#showCompanyLogo").show();
                    }else{
                        $("#showNoCompanyLogo").show();
                    }
                },
                success: function(response) {
                    console.log(response);
                    var resData = JSON.parse(response);
                    if (!resData.Error){
                        var imgSrc = '../../../doorphpscript/comp_logo/' + resData.Data[0].file_name; //image src
                        _user_img = imgSrc;
                        $("#showUploadingCompanyLogo").hide();
                        $("#showCompanyLogo").show();
                        $("#imgCoLogo").attr('src', imgSrc);
                        //reset the form
                        $('#uploadCompanyLogoForm').get(0).reset();
                        $('#removeCompanyLogo').css('display', 'inline-block');
                    }else{
                        $("#companyLogoError").text(resData.ErrorDesc);
                        $("#companyLogoError").show();
                        if (_user_img){
                            $("#showCompanyLogo").show();
                        }else{
                            $("#showNoCompanyLogo").hide();
                        }
                        $("#showUploadingCompanyLogo").hide();
                        $('#removeCompanyLogo').css('display', 'inline-block');
                    }
                }
            });
            return false;
        }



        loadEditCompanyProfile();
    }]);
