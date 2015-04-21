'use strict';

/* Controllers */

angular.module('eyexApp.galleryController', []).
  controller('galleryController', ['$scope', '$http', 'pageServices', function ($scope, $http, pageServices) {
        var _data = '';
        $scope.popupImage = function(imgUrl, title){
            $("#loadImage").modal();
            $("#imagePopupFrame").prop('src', imgUrl);
            if (title){
                $("#imageTitle").html(title);
            }else{
                $("#imageTitle").html('Image');
            }
        }

        $scope.closeImage = function(){
            $("#loadImage").modal('hide');
        }

        var loadGallery = function() {
            $http.get("/eyex-lite/controllers/gallery/getSDCardImages.php")
                .success(function (data, status, headers, config) {
                    if (data.RowsReturned > 0){
                        _data = data.Data;
                        for(var i = 0; i < _data.length; i ++){
                            var year = _data[i].year;
                            var month = _data[i].month - 1;
                            var day = _data[i].day;
                            var hour = _data[i].hour;
                            var minute = _data[i].min;
                            var second = _data[i].seconds;
                            data.Data[i].full_date = moment({
                                year: year
                                , month : month
                                , day : day
                                , hour : hour
                                , minute: minute
                                , second : second
                            }).format('D MMM YYYY, h:mm a');
                        }
                        console.log(_data);
                        $scope.galleryExists = true;
                        $scope.pictures = data.Data;
                    }else{
                        $scope.galleryNoExists = true;
                    }
                });
        }
        loadGallery();

    }]);
