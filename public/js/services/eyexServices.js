'use strict';

angular.module('eyexApp.eyexServices', []).
    factory('eyexServices', ['$http', function ($http) {
    return {
        checkCompanyLogoExists : function (){
            var testUrl = $.ajax({
                type:"HEAD",
                url: '../../../comp_logo/co-logo.jpg',
                async: false
            })
            if (testUrl.status == 200){
                return true;
            }else{
                return false;
            }
        },
        checkUserProfilePicExists : function (userId){
            var testUrl = $.ajax({
                type:"HEAD",
                url: '../../../data/' + userId + '/profile-pic.jpg',
                async: false
            })
            if (testUrl.status == 200){
                return true;
            }else{
                return false;
            }
        },
        deviceBroadcast : function(callback){
            $http.get("/eyex-lite/controllers/device/deviceBroadcast.php")
                .success(function(data, status, headers, config) {
                    callback(data);
                })
                .error(function(data) {
                    callback(data);
                });;
        },
        rs485Broadcast : function(callback){
            $http.get("/eyex-lite/controllers/device/deviceBroadcast.php")
                .success(function(data, status, headers, config) {
                    callback();
                });
        }
    };

}]);