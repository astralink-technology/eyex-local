'use strict';

/* Controllers */

angular.module('eyexApp.companyController', []).
  controller('companyController', ['$scope', '$http', 'dateTimeServices', 'eyexServices', function ($scope, $http, dateTimeServices, eyexServices) {

        $scope.slideInterval = 5000;
        $scope.reportedToWork = 0;
        $scope.inOffice = 0;
        $scope.away = 0;


        //load announcements
        var loadAnnouncements = function() {
            $scope.loadAnnouncements = true;
            $http.get("/eyex-lite/controllers/announcements/getAnnouncement.php")
                .success(function (data, status, headers, config) {
                    //console.log(data);
                    $scope.loadAnnouncements = false;
                    if (data.RowsReturned > 0){
                        $scope.announcementsExists = true;
                        var announcement = data.Data[0].message;
                        $scope.announcement = announcement;
                    }else{
                        $scope.noAnnouncements = true;
                    }
                });
        }

        var loadCompanyProfile = function(){
            $scope.loadProfile = true;
            var parms = '?AuthorizationLevel=' + 400;
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function (data, status, headers, config) {
                    console.log(data);
                    if (data.RowsReturned > 0){
                        $scope.loadProfile = false;
                        var companyData = data.Data[0];
                        if (companyData.sip_cc && companyData.sip){
                            $scope.profilePhone = true;
                            var companyPhoneString = '+(' + companyData.sip_cc + ') ' + companyData.sip;
                            if (companyData.sip_cc == '1800'){
                                companyPhoneString = companyData.sip_cc + companyData.sip + ' (Toll Free)';
                            }
                            $scope.phone = companyPhoneString;
                        }
                        if (companyData.username){
                            $scope.profileEmail = true;
                            $scope.email = companyData.username;
                        }
                        if (companyData.name){
                            $scope.companyName = companyData.name;
                        }
                        if (
                            companyData.apartment ||
                            companyData.street_1 ||
                            companyData.suite ||
                            companyData.postcode ||
                            companyData.country
                        ){
                            var addressHtml = '';
                            if (companyData.apartment){
                                addressHtml += companyData.apartment + ' ';
                            }
                            if (companyData.street_1){
                                addressHtml += companyData.street_1 + ' ';
                            }
                            if (companyData.suite){
                                addressHtml += '#' + companyData.suite + ', ';
                            }
                            if (companyData.country){
                                addressHtml += companyData.country + ' ';
                            }
                            if (companyData.postcode){
                                addressHtml += companyData.postcode;
                            }
                            $scope.address = addressHtml;
                            $scope.profileAddress = true;
                        }

                        if (companyData.extra_data2 && companyData.extra_data3){
                            $scope.profileWebsite = true;
                            $scope.webAddress = companyData.extra_data2 + companyData.extra_data3;
                            $scope.webAddressLink  = companyData.extra_data2 + companyData.extra_data3;
                        }

                        if (companyData.extra_date_time && companyData.extra_date_time2 && companyData.extra_data) {
                            $scope.profileBizHours = true;

                            if (companyData.extra_data) {
                                var workDaysArray = (companyData.extra_data).split(',');
                                var workHtml = '';
                                var workMon = false;
                                var workTue = false;
                                var workWed = false;
                                var workThu = false;
                                var workFri = false;
                                var workSat = false;
                                var workSun = false;

                                for (var i = 0; i < workDaysArray.length; i++) {
                                    if (workDaysArray[i] == '1') {
                                        workMon = true;
                                    } else if (workDaysArray[i] == '2') {
                                        workTue = true;
                                    } else if (workDaysArray[i] == '3') {
                                        workWed = true;
                                    } else if (workDaysArray[i] == '4') {
                                        workThu = true;
                                    } else if (workDaysArray[i] == '5') {
                                        workFri = true;
                                    } else if (workDaysArray[i] == '6') {
                                        workSat = true;
                                    } else if (workDaysArray[i] == '7') {
                                        workSun = true;
                                    }
                                }

                                //all days
                                if (workMon && workTue && workWed && workThu && workFri && workSat && workSun) {
                                    workHtml += 'Daily, ';
                                } else if (
                                    (workMon && workTue && workWed && workThu && workFri) ||
                                    (workSat && workSun)
                                ) {
                                    if (workMon && workTue && workWed && workThu && workFri) {
                                        //weekdays
                                        workHtml += 'Weekdays, ';
                                    }
                                    if (workSat && workSun) {
                                        //weekends
                                        workHtml += 'Weekends, ';
                                    }
                                } else {
                                    if (workMon) workHtml += 'Monday, ';
                                    if (workTue) workHtml += 'Tuesday, ';
                                    if (workWed) workHtml += 'Wednesday, ';
                                    if (workThu) workHtml += 'Thursday, ';
                                    if (workFri) workHtml += 'Friday, ';
                                    if (workSat) workHtml += 'Saturday, ';
                                    if (workSun) workHtml += 'Sunday, ';
                                }
                                //get the working hours
                                if (companyData.extra_date_time){
                                    workHtml += moment(moment(moment(companyData.extra_date_time).format('YYYY-MM-DDTHH:mm:ss+0000')).local()).format('h:mma') + ' - ';
                                }
                                if (companyData.extra_date_time2){
                                    workHtml += moment(moment(moment(companyData.extra_date_time2).format('YYYY-MM-DDTHH:mm:ss+0000')).local()).format('h:mma');
                                }
                            }else{
                                workHtml = '';
                            }
                            $scope.workHours = workHtml;
                        }

                        //load company url
                        if (companyData.co_logo){
                            $scope.profileLogo = true;
                            $("#profilePicUrl").prop('src', companyData.co_logo)
                        }else{
                            $scope.noLogoUploaded = true;
                        }

                    }
                });
        }

        var loadEcosystem = function(deviceId){
            $scope.loadEcosystem = true;
            if (deviceId){
                var parms = "?DeviceCode=" + deviceId;
                $http.get("http://23.21.214.219/Dashboard/DashboardHandler.php" + encodeURI(parms))
                    .success(function (data, status, headers, config) {
                        $scope.loadEcosystem = false;
                        if (data.totalRowsAvailable > 0){
                            var devices = data.data[0].deviceStatus;
                            var deviceObjects = JSON.parse(devices);
                            if (deviceObjects.length > 0){
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
                                $scope.noEcosystem = true;
                            }
                        }else{
                            $scope.noEcosystem = true;
                        }
                    });
            }else{
                $scope.loadEcosystem = false;
                $scope.noEcosystem = true;
            }
        }

        var loadStaffStrength = function(){
            $scope.loadStaffStrength = true;
            var parms = '?Type=contact';
            parms += '&AuthorizationLevel=300';
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function (data, status, headers, config) {
                    $scope.loadStaffStrength = false;
                    $scope.staffStrengthLoaded = true;
                    $scope.staffStrength = data.RowsReturned;

                    if (data.RowsReturned > 0){
                        //getting reported to work count
                        var reportedToWorkData = $linq(data.Data)
                            .where("x => x.status != 'P'")
                            .where("x => x.status != 'A'")
                            .toArray();

                        var reportedToWorkCount = reportedToWorkData.length;

                        //getting in the office count
                        var inOfficeData = $linq(data.Data)
                            .where("x => x.status == 'I'")
                            .toArray();
                        var inOfficeCount = inOfficeData.length;

                        //getting away count
                        var awayData = $linq(data.Data)
                            .where("x => x.status == 'A'")
                            .toArray();
                        var awayDataCount = awayData.length;


                        $scope.reportedToWork = reportedToWorkCount;
                        $scope.inOffice = inOfficeCount;
                        $scope.away = awayDataCount;
                    }
                });
        }

        var loadStaffInOffice = function(){
            $scope.loadInOffice = true;
            var inofficeParms = '?Status=I';
            inofficeParms += '&Type=contact';
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(inofficeParms))
                .success(function (data, status, headers, config) {
                    //console.log(data);
                    $scope.inOfficeCount = data.RowsReturned;
                    $scope.loadInOffice = false;
                    if (data.RowsReturned > 0){
                        $scope.inOfficeExists = true;
                        $scope.inOfficeStaff = data.Data;
                    }else{
                        $scope.noInOffice = true;
                    }
                });
        }

        var loadStaffAway = function(){
            $scope.loadAway = true;
            var awayParms = '?Status=A';
            awayParms += '&Type=contact';
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(awayParms))
                .success(function (data, status, headers, config) {
                    //console.log(data);
                    $scope.loadAway = false;
                    $scope.awayCount = data.RowsReturned;
                    if (data.RowsReturned > 0){
                        $scope.awayExists = true;
                        $scope.awayStaff = data.Data;
                    }else{
                        $scope.noAway = true;
                    }
                });
        }

        var loadStaffOvertime = function(){
            $scope.loadOvertime = true;
            var overtimeParms = '?Status=O';
            overtimeParms += '&Type=contact';
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(overtimeParms))
                .success(function (data, status, headers, config) {
                    //console.log(data);
                    $scope.overtimeCount = data.RowsReturned;
                    $scope.loadLate = false;
                    if (data.RowsReturned > 0){
                        $scope.lateExists = true;
                    }else{
                        $scope.noLate = true;
                    }
                });
        }

        var loadStaffLate = function(){
            $scope.loadOvertime = true;
            var lateParms = '?Status=L';
            lateParms += '&Type=contact';
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(lateParms))
                .success(function (data, status, headers, config) {
                    //console.log(data);
                    $scope.lateForWorkCount = data.RowsReturned;
                    $scope.loadOvertime = false;
                    if (data.RowsReturned > 0){
                        $scope.overtimeExists = true;
                    }else{
                        $scope.noOvertime = true;
                    }
                });
        }

        //load devices
        var loadDevices = function(){
            $http.get("/eyex-lite/controllers/device/getDevice.php")
                .success(function (data, status, headers, config) {
                    console.log(data);
                    var deviceIdArray = new Array();
                    if (data.RowsReturned > 0){
                        for (var d = 0; d < data.RowsReturned; d++ ){
                            if (data.Data[d].device_type == 'VS'){
                                var deviceIdIn = data.Data[d].device_id + '-888';
                                var deviceIdOut = data.Data[d].device_id + '-999';
                                $scope.VisitorStationId = data.Data[d].device_id;
                                deviceIdArray.push(deviceIdIn);
                                deviceIdArray.push(deviceIdOut);
                            }else if (data.Data[d].device_type == 'RFID'){
                                deviceIdArray.push(data.Data[d].device_id);
                            }else if (data.Data[d].device_type == 'OS'){
                                $scope.OfficeStationId = data.Data[d].device_id;
                            }
                        }
                        loadEcosystem($scope.OfficeStationId);
                    }
                });
        }

        var loadActivities = function(){
            $scope.loadActivities = true;
            var activityParms = '';
            activityParms += '?ClockType=I,O,T,X,N';
            activityParms += '&SkipSize=0';
            activityParms += '&PageSize=15';
            $http.get("/eyex-lite/controllers/access/getAccess.php" + encodeURI(activityParms))
                .success(function (data, status, headers, config) {
                    $scope.loadActivities = false;
                    if (data.RowsReturned > 0){
                        for (var i = 0; i < data.RowsReturned; i ++){
                            data.Data[i].login_time = dateTimeServices.timeSince(moment(moment(data.Data[i].login_time).format('YYYY-MM-DDTHH:mm:ss+0000')).local());
                            if (data.Data[i].clock_type == 'O'){
                                data.Data[i].activity = 'Clocked out';
                            }else if (data.Data[i].clock_type == 'I'){
                                data.Data[i].activity = 'Clocked in';
                            }else if (data.Data[i].clock_type == 'X'){
                                data.Data[i].activity = 'Exited';
                            }else if (data.Data[i].clock_type == 'N'){
                                data.Data[i].activity = 'Entered';
                            }else if (data.Data[i].clock_type == 'T'){
                                data.Data[i].activity = 'Tap and accessed';
                            }
                        }
                        $scope.activitiesExists = true;
                        $scope.activities = data.Data;
                    }else{
                        $scope.noActivities = true;
                    }
                });
        }

        loadAnnouncements();
        loadCompanyProfile();
        loadStaffStrength();
        loadStaffInOffice();
        loadStaffAway();
        loadStaffOvertime();
        loadStaffLate();
        loadDevices();
        loadActivities();
  }]);
