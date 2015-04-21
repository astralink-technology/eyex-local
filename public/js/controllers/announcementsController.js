'use strict';

/* Controllers */

angular.module('eyexApp.announcementsController', []).
    controller('announcementsController', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
        $("#taAnnouncement").focus();
        var loadAnnouncement = function(){
            $('#loadAnnouncement').modal({
                keyboard : false,
                backdrop : 'static'
            })
            $http.get("/eyex-lite/controllers/announcements/getAnnouncement.php")
                .success(function(data, status, headers, config)
                {
                    if (data.RowsReturned > 0){
                        var announcement = data.Data[0].message;
                        var announcementId = data.Data[0].id;
                        $scope.taAnnouncement = announcement;
                        $scope.announcementId = announcementId;
                    }
                    $('#loadAnnouncement').modal('hide');
                    $('#taAnnouncement').focus();
                });
        }

        $scope.postAnnouncement = function(){
            var announcement = $scope.taAnnouncement;
            var parms = new Object();
            parms.Message = announcement;
            $('#updatingAnnouncement').modal({
                keyboard : false,
                backdrop : 'static'
            })
            if ($scope.announcementId){
                parms.AnnouncementId = $scope.announcementId;
                $http.post("/eyex-lite/controllers/announcements/updateAnnouncement.php", parms)
                    .success(function(data){
                        console.log(data);
                        if (!data.Error){
                            $scope.successMessage = 'Announcement updated!';
                            $scope.updateSuccess = true;
                        }else{
                            $scope.overallError = true;
                            $scope.valError = data.ErrorDesc;
                        }
                        $('#updatingAnnouncement').modal('hide');
                        $('#taAnnouncement').blur();
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }else{
                $http.post("/eyex-lite/controllers/announcements/addAnnouncement.php", parms)
                    .success(function(data){
                        console.log(data);
                        if (!data.Error){
                            $scope.successMessage = 'Announcement updated!';
                            $scope.updateSuccess = true;
                        }else{
                            $scope.overallError = true;
                            $scope.valError = data.ErrorDesc;
                        }
                        $('#updatingAnnouncement').modal('hide');
                        $('#taAnnouncement').blur();
                    })
                    .error(function(data) {
                        window && console.log(data);
                    });
            }
        }


        loadAnnouncement();
    }]);
