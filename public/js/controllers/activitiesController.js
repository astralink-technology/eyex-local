'use strict';

/* Controllers */

angular.module('eyexApp.activitiesController', []).
  controller('activitiesController', ['$scope', '$http', 'dateTimeServices', 'pageServices', function ($scope, $http, dateTimeServices, pageServices) {
        $scope.activityFilter = '';
        $scope.selectedEmployee = '';
        $scope.selectedPageSize = 20;
        $scope.selectedSkipSize = 0;


        var goToPage = function(pageNumber){
            $scope.selectedSkipSize = (pageNumber - 1) * $scope.selectedPageSize;
            loadActivities();
        }

        var loadActivities = function(){
            $scope.loadingActivities = true;
            $scope.noActivities = false;
            $scope.activitiesExists = false;
            $scope.downloadCsvShow = false;

            var activityParms = '';
            if ($scope.selectedActivityType){
                activityParms += '?ClockType=' + $scope.selectedActivityType;
            }else{
                activityParms += '?ClockType=I,O,T,X,N';
            }
            if ($scope.selectedEmployee) activityParms += '&UserId=' + $scope.selectedEmployee;
            if ($scope.selectedDoor) activityParms += '&DoorId=' + $scope.selectedDoor;
            if ($scope.selectedPageSize) activityParms += '&PageSize=' + $scope.selectedPageSize;
            if ($scope.selectedSkipSize) activityParms += '&SkipSize=' + $scope.selectedSkipSize;
            $http.get("/eyex-lite/controllers/access/getAccess.php" + encodeURI(activityParms))
                .success(function (data, status, headers, config) {
                    $scope.loadingActivities = false;
                    if (data.RowsReturned > 0){
                        $scope.downloadCsvShow = true;
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

                        if ($scope.selectedPageSize){
                            //load pager
                            $("#activityPager").html('');
                            $scope.currentPage = ($scope.selectedSkipSize / $scope.selectedPageSize) + 1;
                            $scope.numberOfPages = data.Data[0].total_rows / $scope.selectedPageSize;
                            pageServices.loadPager('#activityPager', $scope.numberOfPages, $scope.currentPage);
                            $('.page-number').each(function(){
                                var pageNumber = $(this).attr('data-pagenum');
                                $(this).click(function(){
                                    goToPage(pageNumber);
                                })
                            });
                            $("#nextPage").click(function(){
                                if($scope.currentPage != $scope.numberOfPages){
                                    goToPage($scope.currentPage + 1);
                                }
                            });
                            $("#prevPage").click(function(){
                                if ($scope.currentPage != 1){
                                    goToPage($scope.currentPage -1);
                                }
                            });
                        }else{
                            $("#activityPager").html('');
                        }
                    }else{
                        $("#activityPager").html('');
                        $scope.activities = data.Data;
                        $scope.noActivities = true;
                    }
                });
        }


        var getDoors = function(){
            $http.get("/eyex-lite/controllers/door/getDoor.php")
                .success(function (data, status, headers, config) {
                    if (data.RowsReturned > 0){
                        //console.log(data.Data);
                        for (var d = 0; d < data.RowsReturned; d++){
                            var doorEntry = '<option value="' + data.Data[d].door_id + '">' + data.Data[d].door_name + '</option>'
                            $("#ddlDoors").append(doorEntry);
                        }
                    }
                });
        }

        var getEmployees = function(){
            $('#loadEmployeesModal').modal({
                backdrop: 'static'
                , keyboard : false
            });
            var parms = '?Type=contact';
            parms += '&AuthorizationLevel=300';
            $http.get("/eyex-lite/controllers/users/getUserDetails.php" + encodeURI(parms))
                .success(function(data, status, headers, config)
                {
                    //console.log(data.Data);
                    for (var e = 0; e < data.RowsReturned; e++){
                        var employeeEntry = '<option value="' + data.Data[e].id + '">' + data.Data[e].name + '</option>'
                        $("#ddlEmployees").append(employeeEntry);
                    }
                });
        }

        $scope.exportCsv = function(){
            var parms = '?UserId=' + $scope.selectedEmployee;
            if ($scope.selectedDoor) parms += '&DoorId=' + $scope.selectedDoor;
            if ($scope.selectedActivityType) parms += '&ClockType=' + $scope.selectedActivityType;
            $.get("/eyex-lite/controllers/access/getAccessCsv.php", parms ,function(){
                document.location.href = "/eyex-lite/controllers/access/getAccessCsv.php" + parms;
            });
        }

        $('#ddlEmployees').on('change', function(){
            var selectedEmployee = $(this).val();
            $scope.selectedEmployee = selectedEmployee;
            loadActivities();
        })

        $('#ddlDoors').on('change', function(){
            var selectedDoor = $(this).val();
            $scope.selectedDoor = selectedDoor;
            loadActivities();
        })

        $("#ddlActivity").on('change', function(){
            var selectActivityType = $(this).val();
            $scope.selectedActivityType = selectActivityType;
            loadActivities();
        });

        $("#ddlPageSize").on('change', function(){
            var selectedPageSize = $(this).val();
            $scope.selectedPageSize = selectedPageSize;
            loadActivities()
        })


        loadActivities();
        getEmployees();
        getDoors();

    }]);
