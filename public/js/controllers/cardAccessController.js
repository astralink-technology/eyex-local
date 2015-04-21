'use strict';

/* Controllers */

angular.module('eyexApp.cardAccessController', []).
  controller('cardAccessController', ['$scope', '$http', function ($scope, $http) {
        $('#tbCardId').focus();

        var loadCard = function(){
            $("#modalLoadCard").modal({
                backdrop: 'static',
                keyboard: false
            })
            var parms = '?UserId=' + _user_id;
            $http.get("/eyex-lite/controllers/users/getUser.php" + encodeURI(parms))
                .success(function(data, status, headers, config) {
                    $("#modalLoadCard").modal('hide');
                    if (data.RowsReturned > 0){
                        $scope.tbCardId = data.Data[0].card_id;
                    }
                });
        }

        $scope.changeCard = function(){
            var cardId = $scope.tbCardId;
            $("#modalChangeCard").modal({
                backdrop: 'static',
                keyboard: false
            })

            $('input').each(function(){
                $(this).blur();
            });

            //check if pin matches
            var parms = new Object();
            parms.CardId = cardId;
            if (_user_id) parms.UserId = _user_id;
            $http.post("/eyex-lite/controllers/users/updateCard.php", parms)
                .success(function (data) {
                    console.log(data);
                    if (!data.Error) {
                        $("#modalChangeCard").modal('hide');
                        $scope.successMessage = 'Access Card ID changed successfully';
                        $scope.updateSuccess = true;
                        $('#tbCardId').focus();
                        loadCard();
                    } else {
                        $("#modalChangeCard").modal('hide');
                        $("#tbCardId").focus();
                        if (data.ErrorDesc == 'CardId In Use'){
                            $scope.cardIdError = true;
                            $scope.valCardId = 'Card ID has been taken. Is this your card?';
                        }
                    }
                })
                .error(function (data) {
                    window && console.log(data);
                });
        }

        loadCard();
  }]);
