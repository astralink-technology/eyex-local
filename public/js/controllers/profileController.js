'use strict';

/* Controllers */

angular.module('eyexApp.profileController', []).
  controller('profileController', ['$scope', '$http', 'dateTimeServices', 'statisticsServices', 'stringServices',  function ($scope, $http, dateTimeServices, statisticsServices, stringServices) {
        $scope.clockInLoaded = false;
        $scope.clockOutLoaded = false;
        $scope.clockInExist = false;
        $scope.clockOutExist = false;

        $scope.clockOutCount = 0;
        $scope.usuallyDoesntClockOut = false;

        //time attedance
        var loadTimeAttendance = function(){
            $scope.clockedHoursNoExist = true;
        }

        //get user profile
        var loadUserProfile = function(){
            $scope.loadingProfileDetails = true;
            var userId = _user_id;
            if (_targetUserId){
                userId = _targetUserId;
            }
            var userParms = '?UserId=' + userId;
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(userParms))
                .success(function (data, status, headers, config) {
                    if (data.RowsReturned > 0){
                        if (data.Data[0].archive == 1){
                            window.location = "/eyex-lite/error/not-found.php";
                        }else{
                            var employeeData = data.Data[0];
                            //console.log(employeeData);
                            $scope.loadingProfileDetails = false;
                            $scope.profileDetails = true;
                            //loading of the data
                            $scope.profileName = employeeData.name;
                            $scope.profileStatus = employeeData.status;
                            $scope.email = employeeData.username;
                            if (
                                employeeData.apartment ||
                                employeeData.street_1 ||
                                employeeData.suite ||
                                employeeData.country ||
                                employeeData.postcode
                            ){
                                var addressString = '';
                                if (employeeData.apartment) addressString += employeeData.apartment + ' ';
                                if (employeeData.street_1) addressString += employeeData.street_1 + ' ';
                                if (employeeData.suite) addressString += employeeData.suite + ' ';
                                if (employeeData.country) addressString += employeeData.country + ' ';
                                if (employeeData.postcode) addressString += employeeData.postcode;
                                $scope.address = addressString;
                            }else{
                                $scope.address = null;
                            }

                            $scope.primaryPhone = null;
                            $scope.secondaryPhone = null;
                            if (employeeData.sip && employeeData.sip_cc){
                                var countryCode = employeeData.sip;
                                if (employeeData.sip != '1800'){
                                    countryCode = '+(' + employeeData.sip_cc + ')';
                                }
                                var phoneString = countryCode + ' ' + employeeData.sip;
                                $scope.primaryPhone = phoneString;
                            }
                            if (employeeData.sip2 && employeeData.sip2_cc){
                                var countryCode = employeeData.sip2;
                                if (employeeData.sip2 != '1800'){
                                    countryCode = '+(' + employeeData.sip2_cc + ')';
                                }
                                var phoneString = countryCode + ' ' + employeeData.sip2;
                                $scope.secondaryPhone = phoneString;
                            }

                            $scope.extension = null;

                            if (employeeData.profile_picture){
                                $("#profilePicture").prop('src', employeeData.profile_picture);
                            }else{
                                $("#profilePicture").prop('src', '/eyex-lite/public/img/avatar-rect-md.png');
                            }

                            if (employeeData.extension_number){
                                //get the company details
                                $http.get("/eyex-lite/controllers/company/getCompanyDetails.php")
                                    .success(function (data, status, headers, config) {
                                        if (data.RowsReturned > 0){
                                            var companyData = data.Data[0];
                                            if (companyData.sip && companyData.sip_cc){
                                                var countryCode = companyData.sip;
                                                if (companyData.sip != '1800'){
                                                    countryCode = '+(' + companyData.sip_cc + ')';
                                                }
                                                var phoneString = countryCode + ' ' + companyData.sip;
                                                $scope.companyPhone = phoneString;
                                                $scope.extension = 'ext. ' + employeeData.extension_number;
                                            }
                                        }
                                    });
                            }
                        }
                    }else{
                        window.location = "/eyex-lite/error/not-found.php";
                    }
                });
        }

        var getRecentActivities = function(){
            $scope.loadingActivities = true;
            var userId = _user_id;
            if (_targetUserId){
                userId = _targetUserId;
            }
            var activityParms = '?UserId=' + userId;
            activityParms += '?ClockType=I,O,T,X,N';
            activityParms += '&SkipSize=0';
            activityParms += '&PageSize=15';
            $http.get("/eyex-lite/controllers/access/getAccess.php" + encodeURI(activityParms))
                .success(function (data, status, headers, config) {
                    $scope.loadingActivities = false;
                    if (data.RowsReturned > 0) {
                        $scope.activitiesExist = true;
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
                        $scope.activities = data.Data;
                    }else{
                        $scope.activitiesNoExist = true;
                    }
                });
        }

        var getClockInOutActivity = function(){
            var userId = _user_id;
            if (_targetUserId){
                userId = _targetUserId;
            }
            var today = moment(moment({hour:0, minute:0}).utc()).format('YYYY-MM-DDTHH:mm:ss');
            var todayEnd = moment(moment({hour:23, minute:59}).utc()).format('YYYY-MM-DDTHH:mm:ss');
            var activityParms = '?UserId=' + userId;
            activityParms += '&ClockType=I,O';
            activityParms += '&LoginTimeStart=' + today;
            activityParms += '&LoginTimeEnd=' + todayEnd;
            $http.get("/eyex-lite/controllers/access/getAccess.php" + encodeURI(activityParms))
                .success(function (data, status, headers, config) {
                    if (data.RowsReturned > 0) {
                        var clockInActivities = $linq(data.Data)
                            .where("x => x.clock_type == 'I'")
                            .toArray();

                        var clockOutActivities = $linq(data.Data)
                            .where("x => x.clock_type == 'O'")
                            .toArray();

                        if (clockInActivities.length > 0){
                            //get the first clock in
                            var clockInTime = $linq(clockInActivities).min("x => x.login_time");
                            $scope.clockInTime = moment(moment(moment(clockInTime).format('YYYY-MM-DDTHH:mm:ss+0000')).local()).format('h:mma');
                            $scope.clockInExist = true;
                        }else{
                            $scope.clockInNoExist = true;
                        }

                        if (clockOutActivities.length > 0){
                            //get the last activity
                            var lastActivity = $linq(data.Data).maxBy("x => x.login_time");
                            if (lastActivity.clock_type == 'O'){
                                $scope.clockOutExist = true;
                                $scope.clockOutTime = moment(moment(moment(lastActivity.login_time).format('YYYY-MM-DDTHH:mm:ss+0000')).local()).format('h:mma');
                            }else{
                                $scope.clockOutNoExist = true;
                            }
                        }else{
                            $scope.clockOutNoExist = true;
                        }

                        $scope.clockInTimeRaw = data.Data[0].login_time;
                        $scope.clockOutTimeRaw = data.Data[0].login_time;
                    }else{
                        $scope.clockInNoExist = true;
                        $scope.clockOutNoExist = true;
                    }
                    $scope.clockInLoaded = true;
                    $scope.clockOutLoaded = true;
                });
        }

        var getAllClockInTimings = function(){
            var userId = _user_id;
            if (_targetUserId){
                userId = _targetUserId;
            }
            var activityParms = '?UserId=' + userId;
            activityParms += '&SkipSize=0';
            activityParms += '&PageSize=14';
            $http.get("/eyex-lite/controllers/access/getDailyClockInOutTime.php" + encodeURI(activityParms))
                .success(function (data, status, headers, config) {
                    console.log(data);
                    for(var d = 0; d < data.RowsReturned; d ++){
                        var clockInTimeHour = parseInt(moment(moment(data.Data[d].clock_in).format('YYYY-MM-DDTHH:mm:ss')).format('HH'));
                        var clockInTimeMin = parseInt(moment(moment(data.Data[d].clock_in).format('YYYY-MM-DDTHH:mm:ss')).format('m')) / 60;
                        var clockInTimeRaw = clockInTimeHour + clockInTimeMin;
                        var clockOutTimeRaw = 23.99;
                        if (data.Data[d].clock_out){
                            var clockOutTimeHour = parseInt(moment(moment(data.Data[d].clock_out).format('YYYY-MM-DDTHH:mm:ss')).format('HH'));
                            var clockOutTimeMin = parseInt(moment(moment(data.Data[d].clock_out).format('YYYY-MM-DDTHH:mm:ss')).format('m')) / 60;
                            clockOutTimeRaw = clockOutTimeHour + clockOutTimeMin;
                        }
                        data.Data[d].clock_in = clockInTimeRaw;
                        data.Data[d].clock_out = clockOutTimeRaw;
                        data.Data[d].clock_in_date = moment(moment(data.Data[d].clock_in_date).format('YYYY-MM-DDTHH:mm:ss')).format('DD MMM');

                        if (!data.Data[d].clocked_out){
                            $scope.clockOutCount += 1;
                            $scope.usuallyDoesntClockOut = true;
                        }
                    }
                    buildTimeInChart(data.Data);
                    buildTimeOutChart(data.Data);
                });

        }

        var buildTimeInChart = function(timeInOutData){
            if (timeInOutData.length > 0){
                $('#historicalTimeInExists').show();
                var timeInChartArray = new Array();
                var clockInData = new Array();
                var clockInDataRaw = new Array();
                for (var t = 0; t < timeInOutData.length; t++){
                    timeInChartArray.unshift(timeInOutData[t].clock_in_date);
                    clockInData.unshift(timeInOutData[t].clock_in);
                    clockInDataRaw.unshift(timeInOutData[t].clock_in);
                }

                var clockInMedianTime = new Array();
                var medianClockInTime = statisticsServices.median(clockInDataRaw);
                var medianClockInTimeHour = Math.floor(medianClockInTime);
                var medianClockInTimeMins = medianClockInTime - Math.floor(medianClockInTime);

                //convert to quarter
                if (medianClockInTimeMins * 60 > 45){
                    medianClockInTimeHour += 1;
                    medianClockInTimeMins = 0;
                }else{
                    medianClockInTimeMins = (((((medianClockInTimeMins * 60) + 7.5)/30 | 0) * 30) % 60) / 60;
                }
                medianClockInTime = medianClockInTimeHour + medianClockInTimeMins;

                for (var mt = 0; mt < timeInOutData.length; mt++){
                    clockInMedianTime.push(medianClockInTime);
                }


                if (medianClockInTime){
                    var medianHour =  Math.floor(medianClockInTime);
                    var medianMinute = Math.floor(medianClockInTime % 1 * 60);
                    var medianClockInTimeFormatted = moment({hour: medianHour, minute: medianMinute}).format("h:mm a");
                    $scope.usuallyClocksIn = "Clocks in " + medianClockInTimeFormatted;
                }else{
                    $scope.usuallyClocksIn = '--';
                }

                $('#clockInStats').highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: timeInChartArray
                    },
                    yAxis: {
                        title: {
                            text: 'Time'
                        },
                        labels: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        line: {
                            enableMouseTracking: true
                        }
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        formatter: function() {
                            if (this.points[0] && this.points[1]){
                                var hour =  Math.floor(this.points[0].y);
                                var minute = Math.floor(this.points[0].y % 1 * 60);
                                var medianHour =  Math.floor(this.points[1].y);
                                var medianMinute = Math.floor(this.points[1].y % 1 * 60);

                                if (hour >= 24){
                                    hour -= 24;
                                }
                                if (medianHour >= 24){
                                    medianHour -= 24;
                                }

                                var title = "<b>" + this.x + "</b><br />";
                                var body = "<p>Clocks in at <b>" + moment({hour: hour, minute: minute}).format("h:mm a") + "</b></p><br/>";
                                body += "<p>Usually Clocks In Around <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p>";

                            }else{
                                var medianHour =  Math.floor(this.points[0].y);
                                var medianMinute = Math.floor(this.points[0].y % 1 * 60);

                                if (medianHour >= 24){
                                    medianHour -= 24;
                                }

                                var title = "<b>" + this.x + "</b><br />";
                                var body = "<p>Usually Clocks In <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p>";

                            }
                            return title + body;
                        }
                    },
                    series: [{
                        name: 'Clock In',
                        data: clockInData,
                        color: '#769600',
                        dataLabels: {
                            enabled: true,
                            formatter: function(){
                                if (this.y){
                                    var hour =  Math.floor(this.y);
                                    var minute = Math.floor(this.y % 1 * 60);
                                    if (hour >= 24){
                                        hour -= 24;
                                    }
                                    return moment({hour: hour, minute: minute}).format("h:mm a");
                                }
                            }
                        }
                    }
                        ,{
                            name: 'Usually Clock In',
                            data: clockInMedianTime,
                            color: '#364600',
                            dataLabels: {
                                enabled: false,
                                formatter: function(){
                                    if (this.y){
                                        var hour =  Math.floor(this.y);
                                        var minute = Math.floor(this.y % 1 * 60);
                                        if (hour >= 24){
                                            hour -= 24;
                                        }
                                        return moment({hour: hour, minute: minute}).format("h:mm a");
                                    }
                                }
                            }
                        }
                    ]
                });
            }else{
                $('#historicalTimeInExists').hide();
                $scope.historicalTimeInNoExists = true;
            }
        }

        var buildTimeOutChart = function(timeInOutData){
            if (timeInOutData.length > 0){
                $('#historicalTimeOutExists').show();
                var timeOutChartArray = new Array();
                var clockOutData = new Array();
                var clockOutDataRaw = new Array();
                for (var t = 0; t < timeInOutData.length; t++){
                    timeOutChartArray.unshift(timeInOutData[t].clock_in_date);
                    var clockOutDataObject = new Object();
                    if (!timeInOutData[t].clocked_out){
                        clockOutDataObject.y = null;
                        clockOutData.unshift(clockOutDataObject);
                    }else{
                        clockOutDataObject.y = timeInOutData[t].clock_out;
                        clockOutDataObject.clock_out_next_day = timeInOutData[t].clock_out_next_day;
                        clockOutDataObject.clock_out_next_day_offset = timeInOutData[t].clock_out_next_day_offset;
                        clockOutData.unshift(clockOutDataObject);
                    }
                    clockOutDataRaw.unshift(timeInOutData[t].clock_out);
                }

                var clockOutMedianTime = new Array();
                var medianClockOutTime = statisticsServices.median(clockOutDataRaw);
                for (var mt = 0; mt < timeInOutData.length; mt++){
                    clockOutMedianTime.push(medianClockOutTime);
                }

                //get today's date string
                var todayDateString = moment().format('DD MMM');
                if (todayDateString == timeOutChartArray[timeOutChartArray.length-1] ){
                    var clockedOutToday = timeInOutData[0].clocked_out;
                    if (!clockedOutToday){
                        timeOutChartArray.pop();
                        clockOutData.pop();
                        clockOutMedianTime.pop();
                    }
                }

                if (medianClockOutTime){
                    var medianHour =  Math.floor(medianClockOutTime);
                    var medianMinute = Math.floor(medianClockOutTime % 1 * 60);
                    var medianClockOutTimeFormatted = moment({hour: medianHour, minute: medianMinute}).format("h:mm a");
                    if ($scope.clockOutCount > 7){
                        $scope.usuallyClocksOut = "Doesn't clock out";
                    }else{
                        $scope.usuallyClocksOut = "Clocks out " + medianClockOutTimeFormatted;
                    }
                }else{
                    $scope.usuallyClocksOut = '--';
                }
                $('#clockOutStats').highcharts({
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: timeOutChartArray
                    },
                    yAxis: {
                        title: {
                            text: 'Time'
                        },
                        labels: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        line: {
                            enableMouseTracking: true
                        }
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        formatter: function() {
                            if (this.points[0] && this.points[1]){
                                var hour =  Math.floor(this.points[0].y);
                                var minute = Math.floor(this.points[0].y % 1 * 60);
                                var medianHour =  Math.floor(this.points[1].y);
                                var medianMinute = Math.floor(this.points[1].y % 1 * 60);

                                if (hour >= 24){
                                    hour -= 24;
                                }
                                if (medianHour >= 24){
                                    medianHour -= 24;
                                }

                                var title = "<b>" + this.x + "</b><br />";
                                var body = '';
                                if(this.points[0].point.clock_out_next_day){
                                    body +="<p>Clocks Out at <b>" + moment({hour: hour, minute: minute}).format("h:mm a") + " (+" + this.points[0].point.clock_out_next_day_offset + ' ' + stringServices.pluralize(this.points[0].point.clock_out_next_day_offset, 'day') + ")</b></p><br />";
                                }else{
                                    body +="<p>Clocks Out at <b>" + moment({hour: hour, minute: minute}).format("h:mm a") + "</b></p><br />";
                                }
                                if ($scope.usuallyDoesntClockOut){
                                    body += "<p>Usually doesn't clock out. System takes default knock off hours - <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p>";
                                }else{
                                    body += "<p>Usually Clocks Out <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p>"
                                }

                            }else{
                                var medianHour =  Math.floor(this.points[0].y);
                                var medianMinute = Math.floor(this.points[0].y % 1 * 60);

                                if (medianHour >= 24){
                                    medianHour -= 24;
                                }

                                var title = "<b>" + this.x + "</b><br />";
                                var body = "<p><b>Did not clock out</b></p><br />";
                                if ($scope.usuallyDoesntClockOut){
                                    body += "<p>Usually doesn't clock out. System takes default knock off hours - <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p><br />";
                                }else{
                                    body += "<p>Usually Clocks Out <b>" + moment({hour: medianHour, minute: medianMinute}).format("h:mm a") + "</b></p><br/>"
                                }

                            }
                            return title + body;
                        }
                    },
                    series: [{
                        name: 'Clock Out',
                        data: clockOutData,
                        color: '#930023',
                        dataLabels: {
                            enabled: true,
                            formatter: function(){
                                if (this.y){
                                    var hour =  Math.floor(this.y);
                                    var minute = Math.floor(this.y % 1 * 60);
                                    if (hour >= 24){
                                        hour -= 24;
                                    }
                                    if (this.point.clock_out_next_day){
                                        return moment({hour: hour, minute: minute}).format("h:mm a") + ' (+' + this.point.clock_out_next_day_offset + ' ' + stringServices.pluralize(this.point.clock_out_next_day_offset, 'day') + ')';
                                    }else{
                                        return moment({hour: hour, minute: minute}).format("h:mm a");
                                    }
                                }
                            }
                        }
                    }
                        ,{
                            name: 'Usually Clock Out',
                            data: clockOutMedianTime,
                            color: '#340000',
                            dataLabels: {
                                enabled: false,
                                formatter: function(){
                                    if (this.y){
                                        var hour =  Math.floor(this.y);
                                        var minute = Math.floor(this.y % 1 * 60);
                                        if (hour >= 24){
                                            hour -= 24;
                                        }
                                        return moment({hour: hour, minute: minute}).format("h:mm a");
                                    }
                                }
                            }
                        }
                    ]
                });
            }else{
                $('#historicalTimeOutExists').hide();
                $scope.historicalTimeOutNoExists = true;
            }
        }

        loadUserProfile();
        getRecentActivities();;
        getClockInOutActivity();;
        loadTimeAttendance();
        getAllClockInTimings();
  }]);
