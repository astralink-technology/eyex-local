'use strict';

/* Controllers */

angular.module('eyexApp.devicesController', []).
  controller('devicesController', ['$scope', '$http', 'validationServices', function ($scope, $http, validationServices) {

        var _cardReaders = 0;

        $scope.addDeviceType = null;
        $scope.deviceListing = true;

        $scope.toggleDoors = function(){
            $scope.doorListing = true;
            $scope.deviceListing = false;
            $('.nav-stacked li').removeClass('active');
            $("#navDoors").addClass('active');
            loadDoors();
        }

        $scope.toggleDevices = function(){
            $scope.deviceListing = true;
            $scope.doorListing = false;
            $('.nav-stacked li').removeClass('active');
            $("#navDevices").addClass('active');
            loadDevices();
        }

        $( ".tb-mac-address" ).each(function( index ) {
            $(this).keyup(function(event){
                if ($(this).val().length == 2){
                    if ($(this).parent().next().find('[type="text"]').length > 0){
                        $(this).parent().next().find('[type="text"]')[0].focus();
                    }
                }
            });
        });

        var loadDoors = function(){
            $("#loadDoors").modal({
                keyboard:false,
                backdrop: 'static'
            })
            $http.get("/eyex-lite/controllers/door/getDoor.php")
                .success(function (data, status, headers, config) {
                    $("#loadDoors").modal('hide');
                    if (!data.Error && data.RowsReturned > 0){
                        $scope.doors = data.Data;
                    }
                });
        }

        var refreshAddDoorForm = function(){
            $scope.tbDoorName = '';
            $scope.tbDoorNode = '';
            $('#doorNameError').hide();
            $('#doorNodeError').hide();

            $scope.addDoorForm = true;
            $scope.addingDoor = false;
        }

        var refreshAddDeviceDoorList = function(){
            //get the doors that has not been used up
            $http.get("/eyex-lite/controllers/door/getDoor.php")
                .success(function (data, status, headers, config) {
                    console.log(data);
                    var html = '<option value="">Select Door</option>';
                    if (data.RowsReturned > 0){
                        for (var ud = 0; ud < data.RowsReturned; ud ++){
                            html += '<option value="' + data.Data[ud].door_id + '">' + data.Data[ud].door_name + '</option>';
                        }
                    }
                    $('#ddlRfidAddDoor').html(html);
                    $('#ddlRfidPinAddDoor').html(html);
                    $('#ddlVsAddDoor').html(html);
                });
        }

        var refreshEditDeviceDoorList = function(doorId){
            $("#ddlEditClockType").val('');
            //get the doors that has not been used up
            var parms = '?DeviceId=' + $scope.editDeviceId;
            $http.get("/eyex-lite/controllers/door/getDoor.php" + encodeURI(parms))
                .success(function (data, status, headers, config) {
                    if (data.RowsReturned > 0){
                        var html = '<option value="">Select Door</option>';
                        for (var ud = 0; ud < data.RowsReturned; ud ++){
                            html += '<option value="' + data.Data[ud].door_id + '">' + data.Data[ud].door_name + '</option>';
                        }
                        $('#ddlEditDoor').html(html);
                    }
                    $('#ddlEditDoor').val(doorId);
                });
        }

        var refreshAddDeviceList = function(){
            $scope.addDeviceFormContent = true;
            $scope.tbRfidDeviceName = '';
            $scope.tbRfidDeviceId = '';
            $scope.tbRfidPinDeviceName = '';
            $scope.tbRfidPinDeviceId = '';
            $("#ddlRfidPinClocktype").val('');
            $("#ddlRfidClocktype").val('');
            $('.tb-mac-address').each(function(){
                $(this).val('');
            })
            $('.tb-device-name').each(function(){
                $(this).val('');
            })
            $("#ddlDeviceTypes").html('<option value="">Choose device type</option>');
            $http.get("/eyex-lite/controllers/device/getDeviceDoorDetails.php")
                .success(function (data, status, headers, config) {
                    $("#loadDevices").modal('hide');
                    $(".add-device-form").hide();
                    $scope.devices = data.Data;
                    var officeStationExists = false;
                    var visitorStationExists = false;
                    if (data.RowsReturned > 0) {
                        for (var i = 0; i < data.Data.length; i++) {
                            if (data.Data[i].device_type == 'OS') {
                                officeStationExists = true;
                            } else if (data.Data[i].device_type == 'VS') {
                                visitorStationExists = true;
                            }
                        }
                    }
                    if (_cardReaders < 8){
                        var options = '<option value="CR">Card Reader</option>';
                        options += '<option value="PCR">PIN Card Reader</option>';
                    }
                    if (!officeStationExists) {
                        options += '<option value="OS">Office Station</option>';
                    }
                    if (!visitorStationExists) {
                        options += '<option value="VS">Visitor Station</option>';
                    }
                    $("#ddlDeviceTypes").append(options);
                });
        }

        $("#ddlDeviceTypes").change(function(){
            $('.alert-sm').hide();
            $("#addOfficeStation").hide();
            $("#addVisitorStation").hide();
            $("#addRfidCardReader").hide();
            $("#addRfidPinCardReader").hide();
            var addDeviceType = $("#ddlDeviceTypes").val();
            if (addDeviceType){
                $("#btnAddDevice").show();
                if (addDeviceType == 'OS'){
                    $("#addOfficeStation").show();
                    $scope.addDeviceType = 'OS';
                }else if (addDeviceType == 'VS'){
                    $("#addVisitorStation").show();
                    $scope.addDeviceType = 'VS';
                }else if (addDeviceType == 'CR'){
                    $("#addRfidCardReader").show();
                    $scope.addDeviceType = 'CR';
                }else if (addDeviceType == 'PCR'){
                    $("#addRfidPinCardReader").show();
                    $scope.addDeviceType = 'PCR';
                }else{
                    $scope.addDeviceType = null;
                }
            }else{
                $("#btnAddDevice").hide();
            }
            refreshAddDeviceDoorList();
        });

        var loadDevices = function(){
            _cardReaders = 0;
            $("#loadDevices").modal({
                keyboard:false,
                backdrop: 'static'
            })
            $http.get("/eyex-lite/controllers/device/getDeviceDoorDetails.php")
                .success(function (data, status, headers, config) {
                    $("#loadDevices").modal('hide');
                    $scope.devices = data.Data;
                    var officeStationExists = false;
                    var visitorStationExists = false;
                    if (data.RowsReturned > 0){
                        for (var i = 0; i < data.Data.length; i ++) {
                            if (data.Data[i].device_type == 'OS'){
                                officeStationExists = true;
                            }else if (data.Data[i].device_type == 'VS'){
                                visitorStationExists = true;
                            }
                            if (data.Data[i].device_type == 'PCR' || data.Data[i].device_type == 'CR'){
                                _cardReaders += 1;
                            }
                        }
                    }
                    var options = '<option value="CR">RFID Card Reader</option>';
                    options += '<option value="PCR">RFID PIN Card Reader</option>';
                    if (!officeStationExists){
                        options += '<option value="OS">Office Station</option>';
                    }
                    if (!visitorStationExists){
                        options += '<option value="VS">Visitor Station</option>';
                    }
                    $("#ddlDeviceTypes").append(options);

                    setTimeout(function(){
                        $('.badge').each(function(){
                            $(this).tooltip();
                        })
                    }, 300);

                    //count the number of pin card readers and readers
                });
        }

        var assignEmployeeAuth = function(callback){
            //assign the authorization
            var authorizedArray = new Array();
            $('.user-authorized').each(function(){
                if ($(this).is(':checked')){
                    var userId = $(this).val();
                    authorizedArray.push(userId);
                }
            });
            var userIdArray = JSON.stringify(authorizedArray);
            var parms = new Object();
            parms.DeviceId = $scope.editDeviceId;
            parms.UserIds = userIdArray;
            $http.post("/eyex-lite/controllers/deviceRelationship/assignDeviceEmployeeAuth.php", parms)
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

        $scope.editDoor = function(){
            var doorName = $scope.tbEditDoorName;
            var doorNode = $scope.tbEditDoorNode;

            var validateDoorName = validationServices.validateRequiredField(doorName);
            var validateDoorNode = validationServices.validateRequiredField(doorNode);

            if (validateDoorName){
                $scope.editDoorNameError = true;
                $scope.valEditDoorName = validateDoorName;
            }else{
                $scope.valEditDoorName = '';
                $scope.editDoorNameError = false;
            }

            if (validateDoorNode){
                $scope.editDoorNodeError = true;
                $scope.valEditDoorNode = validateDoorNode;
            }else{
                $scope.valEditDoorNode = '';
                $scope.editDoorNodeError = false;
            }

            if (
                validateDoorName ||
                validateDoorNode
            ){
                return;
            }else{
                $scope.editDoorContent = false;
                $scope.editingDoorContent = true;

                var editDoorParms = new Object();
                editDoorParms.DoorId = $scope.editDoorId;
                editDoorParms.DoorName = doorName;
                editDoorParms.DoorNode = doorNode;
                $http.post("/eyex-lite/controllers/door/updateDoor.php", editDoorParms)
                    .success(function(data){
                        $scope.editingDoorContent = false;
                        if (!data.Error){
                            $("#editDoorForm").modal('hide');
                            loadDoors();
                        }else{
                            if (data.ErrorDesc == 'Node In Use'){
                                $scope.editDoorNodeError = true;
                                $scope.valEditDoorNode = 'Door Node already taken';
                            }else{
                                window && console.log(data);
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.editDevice = function(){
            var deviceName = $scope.tbEditDeviceName;
            var doorId = $("#ddlEditDoor").val();
            var deviceClockType = $('#ddlEditClockType').val();

            var validateEditDeviceName = validationServices.validateRequiredField(deviceName);
            if (validateEditDeviceName){
                $scope.editDeviceNameError = true;
                $scope.valEditDeviceName = validateEditDeviceName;
            }else{
                $scope.valEditDeviceName = '';
                $scope.editDeviceNameError = false;

            }
            if (validateEditDeviceName){
                return;
            }else{
                $scope.editDeviceContent = false;
                $scope.editingDevice = true;

                //edit the device first
                var editDeviceParms = new Object();
                editDeviceParms.DeviceId = $scope.editDeviceId;
                editDeviceParms.DeviceName = deviceName;
                if (deviceClockType || deviceClockType == '') editDeviceParms.Type3 = deviceClockType;
                $http.post("/eyex-lite/controllers/device/updateDevice.php", editDeviceParms)
                    .success(function(data){
                        if (!data.Error){
                            if ($scope.editDeviceTypeRaw == 'OS'){
                                $("#editDeviceForm").modal('hide');
                                loadDevices();
                            }else{
                                assignEmployeeAuth(function(){
                                    //pair doors
                                    pairDoor($scope.editDeviceId, doorId, function(){
                                        $scope.editingDevice = false;
                                        $("#editDeviceForm").modal('hide');
                                        loadDevices();
                                    })
                                })
                            }
                        }else{
                            window && console.log(data);
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });

            }
        }

        $scope.editDoorPopup = function(doorId, doorNode, doorName){
            $scope.editDoorContent = true;
            $scope.editDoorFormControls = true;
            $scope.confirmDeleteDoorExists = false;
            $scope.deletingDoor = false;
            $scope.openingDoor = false;

            $scope.editDoorNodeError = false;
            $("#editDoorForm").modal({
                keyboard:false,
                backdrop: 'static'
            });
            $scope.editDoorId= doorId;
            $scope.editDoorNode = doorNode;
            $scope.tbEditDoorName = doorName;
            $scope.tbEditDoorNode = doorNode;
        }

        $scope.editDevicePopup = function(deviceType, deviceId, deviceName, doorId, doorPurpose){
            $scope.editDeviceContent = true;
            $scope.editDeviceFormControls = true;
            $scope.confirmDeleteDeviceExists = false;
            $scope.noEcosystem = false;
            $scope.noEmployeesAuth = false;
            $scope.employeeAuthExists = false;
            $scope.ecosystemExists = false;
            $("#editDeviceForm").modal({
                keyboard:false,
                backdrop: 'static'
            });
            var deviceTypeName = 'Device';
            if (deviceType == 'OS'){
                deviceTypeName = 'Office Station'
            }else if (deviceType == 'VS'){
                deviceTypeName = 'Visitor Station'
            }else if (deviceType == 'CR'){
                deviceTypeName = 'RFID Card Reader'
            }else if (deviceType == 'PCR'){
                deviceTypeName = 'RFID PIN Card Reader'
            }
            $scope.editDeviceId = deviceId;
            $scope.editDeviceTypeRaw = deviceType;
            $scope.editDeviceType = deviceTypeName;
            $scope.editDeviceName = deviceName;
            $scope.tbEditDeviceName = deviceName;

            //load the list of connected devices
            if (deviceType == 'OS'){
                $scope.btnAuthorizeAll = false;
                $scope.btnDeauthorizeAll = false;
                loadConnectedDevices(deviceId);
            }else if (deviceType == 'VS' || deviceType == 'PCR' || deviceType == 'CR'){
                $scope.btnAuthorizeAll = true;
                $scope.btnDeauthorizeAll = true;
                loadEmployeeAuthList(deviceId);
                refreshEditDeviceDoorList(doorId);
                if (doorPurpose){
                    $("#ddlEditClockType").val(doorPurpose);
                }
            }
        }

        $scope.authorizeAll = function(){
            $('.user-authorized').each(function(){
                $(this).prop('checked', 'checked');
            })
        }

        $scope.deauthorizeAll = function(){
            $('.user-authorized').each(function(){
                $(this).prop('checked', false);
            })
        }

        var loadConnectedDevices = function(officeStationId){
            $scope.loadEcosystem = true;
            if (officeStationId){
                var parms = "?DeviceCode=" + officeStationId;
                $http.get("http://23.21.214.219/Dashboard/DashboardHandler.php" + encodeURI(parms))
                    .success(function (data, status, headers, config) {
                        $scope.loadEcosystem = false;
                        if (data.totalRowsAvailable > 0){
                            var devices = data.data[0].deviceStatus;
                            var deviceObjects = JSON.parse(devices);
                            if (deviceObjects.length > 0){
                                $scope.ecosystemExistsRaw = true;
                                $scope.ecosystemExists = true;
                                for (var i = 0; i < deviceObjects.length; i ++){
                                    if (deviceObjects[i].nodeName){
                                        deviceObjects[i].name = deviceObjects[i].nodeName;
                                        deviceObjects[i].nodeName = (deviceObjects[i].nodeName).toLowerCase();
                                        deviceObjects[i].icon = null;
                                    }
                                    deviceObjects[i].data = null;
                                    if (deviceObjects[i].nodeName == 'dimmer'){
                                        deviceObjects[i].icon = "/eyex-lite/public/img/power-monitor-xs.png";
                                        deviceObjects[i].data = deviceObjects[i].status;
                                    }else if  (deviceObjects[i].nodeName == 'multi sensor'){
                                        deviceObjects[i].icon = "/eyex-lite/public/img/multisensor-xs.png";
                                    }else if (deviceObjects[i].nodeName == 'power meter'){
                                        deviceObjects[i].icon = "/eyex-lite/public/img/power-meter-xs.png";
                                        deviceObjects[i].data = deviceObjects[i].sensorVal;
                                    }else if (deviceObjects[i].nodeName == 'door lock'){
                                        deviceObjects[i].icon = "/eyex-lite/public/img/doorlock-xs.png";
                                        deviceObjects[i].data = deviceObjects[i].mode;
                                    }else if (deviceObjects[i].nodeName == 'contact sensor'){
                                        deviceObjects[i].icon = "/eyex-lite/public/img/contact-sensor-xs.png";
                                    }

                                }
                                $scope.sensors = deviceObjects;
                            }else{
                                $scope.ecosystemExistsRaw = false;
                                $scope.noEcosystem = true;
                            }
                        }else{
                            $scope.ecosystemExistsRaw = false;
                            $scope.noEcosystem = true;
                        }
                    });
            }else{
                $scope.loadEcosystem = false;
                $scope.noEcosystem = true;
            }
        }

        var loadEmployeeAuthList = function(deviceId){
            $scope.loadEmployeesAuth = true;
            if (deviceId){
                var parms = "?DeviceId=" + deviceId;
                $http.get("/eyex-lite/controllers/users/getEmployeeDeviceAuthentication.php" + encodeURI(parms))
                    .success(function (data, status, headers, config) {
                        $scope.loadEmployeesAuth = false;
                        if (data.RowsReturned > 0){
                            $scope.employeeDeviceAuthentications = data.Data;
                            $scope.employeeAuthExistsRaw = true;
                            $scope.employeeAuthExists = true;
                        }else{
                            $scope.employeeAuthExistsRaw = false;
                            $scope.noEmployeesAuth = true;
                        }
                    });
            }else{
                $scope.employeeAuthExistsRaw = false;
                $scope.loadEmployeesAuth = false;
                $scope.noEmployeesAuth = true;
            }
        }

        var pairDoor = function(deviceId, deviceDoor, callback){
            var parms = new Object();
            parms.DeviceId = deviceId;
            parms.DoorId = deviceDoor;
            $http.post("/eyex-lite/controllers/doorRelationship/pairDoor.php", parms)
                .success(function(data){
                    console.log(data);
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


        $scope.confirmDeleteDoor = function(){
            $scope.editDoorFormControls = false;
            $scope.confirmDeleteDoorExists = true;

        }

        $scope.confirmDeleteDevice = function(){
            $scope.editDeviceFormControls = false;
            $scope.ecosystemExists = false;
            $scope.employeeAuthExists = false;
            $scope.noEcosystem = false;
            $scope.noEmployeesAuth = false;
            $scope.confirmDeleteDeviceExists = true;
        }

        $scope.deleteDoor = function(){
            $scope.confirmDeleteDoorExists = false;
            $scope.deletingDoor = true;
            var parms = new Object();
            parms.DoorId = $scope.editDoorId;
            $http.post("/eyex-lite/controllers/door/deleteDoorDetail.php", parms)
                .success(function(data){
                    $("#editDoorForm").modal('hide');
                    $scope.deletingDoor = false;
                    if (!data.Error){
                        loadDoors();
                    }else{
                        window && console.log(data);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });

        }

        $scope.deleteDevice = function(){
            $scope.confirmDeleteDeviceExists = false;
            $scope.deletingDevice = true;
            var parms = new Object();
            parms.DeviceId = $scope.editDeviceId;
            $http.post("/eyex-lite/controllers/device/deleteDeviceDetail.php", parms)
                .success(function(data){
                    $("#editDeviceForm").modal('hide');
                    $scope.deletingDevice = false;
                    if (!data.Error){
                        loadDevices();
                    }else{
                        window && console.log(data);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.deleteDoorCancel = function(){
            $scope.editDoorFormControls = true;
            $scope.confirmDeleteDoorExists = false;
        }

        $scope.deleteDeviceCancel = function(){
            $scope.confirmDeleteDeviceExists = false;
            $scope.editDeviceFormControls = true;
            var deviceType = $scope.editDeviceTypeRaw;
            var deviceId = $scope.editDeviceId;
            //load the list of connected devices
            if (deviceType == 'OS'){
                if ($scope.ecosystemExistsRaw){
                    $scope.ecosystemExists = true;
                }else{
                    $scope.noEcosystem = true;
                }
                $scope.btnAuthorizeAll = false;
                $scope.btnDeauthorizeAll = false;
            }else if (deviceType == 'VS' || deviceType == 'CR' || deviceType == 'PCR'){
                if ($scope.employeeAuthExistsRaw){
                    $scope.employeeAuthExists = true;
                }else{
                    $scope.noEmployeesAuth = true;
                }
                $scope.btnAuthorizeAll = true;
                $scope.btnDeauthorizeAll = true;
            }
        }

        $scope.addDoor = function(){
            var doorName = $scope.tbDoorName;
            var doorNode = $scope.tbDoorNode;

            var doorNameValidation = validationServices.validateRequiredField(doorName);
            var doorNodeValidation = validationServices.validateAlphaNumeric(doorNode, true);

            if (doorNameValidation){
                $('#doorNameError').show();
                $('#doorNameError').text(doorNameValidation);
            }else{
                $('#doorNameError').hide();
                $('#doorNameError').text('');
            }

            if (doorNodeValidation){
                $('#doorNodeError').show();
                $('#doorNodeError').text(doorNodeValidation);
            }else{
                $('#doorNodeError').hide();
                $('#doorNodeError').text('');
            }

            if (
                doorNodeValidation ||
                doorNameValidation
            ){
                return;
            }else{
                $scope.addDoorForm = false;
                $scope.addingDoor = true;
                var parms = new Object();
                parms.DoorName = doorName;
                parms.DoorNode = doorNode;
                $http.post("/eyex-lite/controllers/door/addDoor.php", parms)
                    .success(function(data){
                        $scope.addingDoor = false;
                        if (!data.Error){
                            $("#addDoorForm").modal('hide');
                            loadDoors();
                            refreshAddDoorForm();
                        }else{
                            if (data.ErrorDesc == 'Node In Use'){
                                $scope.addDoorForm = true;
                                $('#doorNodeError').show();
                                $('#doorNodeError').text('Door Node already taken');
                            }else{
                                window && console.log(data);
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.addDevice = function(){
            var deviceId = '';
            var deviceName = '';
            var deviceDoor = '';
            var devicePurpose = '';
            if ($scope.addDeviceType == 'OS'){
                deviceName = $scope.tbOsDeviceName;
                var tbOsId1 = $scope.tbOsId1;
                var tbOsId2 = $scope.tbOsId2;
                var tbOsId3 = $scope.tbOsId3;
                var tbOsId4 = $scope.tbOsId4;
                var tbOsId5 = $scope.tbOsId5;
                var tbOsId6 = $scope.tbOsId6;
                if (tbOsId1 &&
                    tbOsId2 &&
                    tbOsId3 &&
                    tbOsId4 &&
                    tbOsId5 &&
                    tbOsId6
                ){
                    deviceId = $scope.tbOsId1 + $scope.tbOsId2 + $scope.tbOsId3 + $scope.tbOsId4 + $scope.tbOsId5 + $scope.tbOsId6;
                }
            }else if ($scope.addDeviceType == 'VS'){
                deviceName = $scope.tbVsDeviceName;
                var tbVsId1 = $scope.tbVsId1;
                var tbVsId2 = $scope.tbVsId2;
                var tbVsId3 = $scope.tbVsId3;
                var tbVsId4 = $scope.tbVsId4;
                var tbVsId5 = $scope.tbVsId5;
                var tbVsId6 = $scope.tbVsId6;
                if (tbVsId1 &&
                    tbVsId2 &&
                    tbVsId3 &&
                    tbVsId4 &&
                    tbVsId5 &&
                    tbVsId6
                ){
                    deviceId = $scope.tbVsId1 + $scope.tbVsId2 + $scope.tbVsId3 + $scope.tbVsId4 + $scope.tbVsId5 + $scope.tbVsId6;
                }
                deviceDoor = $("#ddlVsAddDoor").val();
            }else if ($scope.addDeviceType == 'CR'){
                deviceName = $scope.tbRfidDeviceName;
                deviceId = $scope.tbRfidDeviceId;
                deviceDoor = $("#ddlRfidAddDoor").val();
                devicePurpose = $('#ddlRfidClocktype').val();
            }else if ($scope.addDeviceType == 'PCR'){
                deviceName = $scope.tbRfidPinDeviceName;
                deviceId = $scope.tbRfidPinDeviceId;
                deviceDoor = $("#ddlRfidPinAddDoor").val();
                devicePurpose = $('#ddlRfidPinClocktype').val();
            }

            var deviceIdValidation = validationServices.validateAlphaNumeric(deviceId, true);
            var deviceNameValidation = validationServices.validateRequiredField(deviceName);

            if (deviceNameValidation){
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceNameOsError").show();
                    $("#deviceNameOsError").text(deviceNameValidation);
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceNameVsError").show();
                    $("#deviceNameVsError").text(deviceNameValidation);
                }else if($scope.addDeviceType == 'CR'){
                    $("#deviceNameRfidError").show();
                    $("#deviceNameRfidError").text(deviceNameValidation);
                }else if($scope.addDeviceType == 'PCR'){
                    $("#deviceNameRfidPinError").show();
                    $("#deviceNameRfidPinError").text(deviceNameValidation);
                }
            }else{
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceNameOsError").hide();
                    $("#deviceNameOsError").text('');
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceNameVsError").hide();
                    $("#deviceNameVsError").text('');
                }else if($scope.addDeviceType == 'CR'){
                    $("#deviceNameRfidError").hide();
                    $("#deviceNameRfidError").text('');
                }else if($scope.addDeviceType == 'PCR'){
                    $("#deviceNameRfidPinError").hide();
                    $("#deviceNameRfidPinError").text('');
                }
            }

            if (deviceIdValidation){
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceIdOsError").show();
                    $("#deviceIdOsError").text(deviceIdValidation);
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceIdVsError").show();
                    $("#deviceIdVsError").text(deviceIdValidation);
                }else if($scope.addDeviceType == 'CR'){
                    $("#deviceIdRfidError").show();
                    $("#deviceIdRfidError").text(deviceIdValidation);
                }else if($scope.addDeviceType == 'PCR'){
                    $("#deviceIdRfidPinError").show();
                    $("#deviceIdRfidPinError").text(deviceIdValidation);
                }
                return;
            }else{
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceIdOsError").hide();
                    $("#deviceIdOsError").text('');
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceIdVsError").hide();
                    $("#deviceIdVsError").text('');
                }else if($scope.addDeviceType == 'CR'){
                    $("#deviceIdRfidError").hide();
                    $("#deviceIdRfidError").text('');
                }else if($scope.addDeviceType == 'PCR'){
                    $("#deviceIdRfidPinError").hide();
                    $("#deviceIdRfidPinError").text('');
                }
            }

            if (($scope.addDeviceType == 'VS' || $scope.addDeviceType == 'OS') &&  deviceId.length < 12){
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceIdOsError").show();
                    $("#deviceIdOsError").text("Invalid MAC Address");
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceIdVsError").show();
                    $("#deviceIdVsError").text("Invalid MAC Address");
                }
                return;
            }else{
                if ($scope.addDeviceType == 'OS'){
                    $("#deviceIdOsError").hide();
                    $("#deviceIdOsError").text('');
                }else if($scope.addDeviceType == 'VS'){
                    $("#deviceIdVsError").hide();
                    $("#deviceIdVsError").text('');
                }
            }

            if (
                deviceIdValidation &&
                deviceNameValidation &&
                deviceId.length < 12
            ){
                return;
            }else{
                $scope.addDeviceFormContent = false;
                $scope.addingDevice = true;
                var parms = new Object();
                parms.DeviceId = deviceId;
                parms.DeviceName = deviceName;
                parms.DeviceType = $scope.addDeviceType;
                if (devicePurpose) parms.Type3 = devicePurpose;
                $http.post("/eyex-lite/controllers/device/addDevice.php", parms)
                    .success(function(data){
                        console.log(data);
                        $scope.addingDevice = false;
                        if (!data.Error){
                            if(deviceDoor){
                                pairDoor(deviceId, deviceDoor, function(){
                                    $("#addDeviceForm").modal('hide');
                                    loadDevices();
                                    refreshAddDeviceList();
                                });
                            }else{
                                $("#addDeviceForm").modal('hide');
                                loadDevices();
                                refreshAddDeviceList();
                            }
                        }else{
                            window && console.log(data);
                            if (data.ErrorDesc == 'DeviceId Exists'){
                                $scope.addDeviceFormContent = true;
                                if ($scope.addDeviceType == 'VS'){
                                    $("#deviceIdVsError").show();
                                    $("#deviceIdVsError").text("Device Exists");
                                }else if ($scope.addDeviceType == 'OS'){
                                    $("#deviceIdOsError").show();
                                    $("#deviceIdOsError").text("Device Exists");
                                }else if ($scope.addDeviceType == 'CR'){
                                    $("#deviceIdRfidError").show();
                                    $("#deviceIdRfidError").text("Device Exists");
                                }else if ($scope.addDeviceType == 'PCR'){
                                    $("#deviceIdRfidPinError").show();
                                    $("#deviceIdRfidPinError").text("Device Exists");
                                }
                            }
                        }
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }

        $scope.forceOpenDoor = function(){
            $scope.editDoorFormControls = false;
            $scope.openingDoor = true;
            var parms = new Object();
            parms.DoorNode = $scope.editDoorNode;
            $http.post("/eyex-lite/controllers/door/doorSystemOverride.php", parms)
                .success(function(data){
                    $scope.openingDoor = false;
                    $scope.editDoorFormControls = true;
                    if (!data.Error){
                        window && console.log(data);
                    }else{
                        window && console.log(data);
                    }
                })
                .error(function(data) {
                    window && console.log(data);
                });
        }

        $scope.addDevicePopup = function(){
            refreshAddDeviceList();
            refreshAddDeviceDoorList();
            $("#addDeviceForm").modal({
                keyboard: false,
                backdrop: 'static'
            });
        }

        $scope.addDoorPopup = function(){
            refreshAddDoorForm();
            $("#addDoorForm").modal({
                keyboard: false,
                backdrop: 'static'
            });
        }

        $scope.closeAddDoorPopup = function(){
            $("#addDoorForm").modal('hide');
        }
        $scope.closeAddDevicePopup = function(){
            $("#addDeviceForm").modal('hide');
        }
        $scope.closeEditDevicePopup = function(){
            $("#editDeviceForm").modal('hide');
        };
        $scope.closeEditDoorPopup = function(){
            $("#editDoorForm").modal('hide');
        };

        loadDevices();
    }]);
