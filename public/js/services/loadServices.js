'use strict';

angular.module('eyexApp.loadServices', []).
    factory('loadServices', function () {
        return {
            //ajaxLoader
            loadAjaxLoader: function (loadPrefix, loadTag, title, text, backdrop, keyboard) {
                var ajaxLoader = '<div class="modal fade ajax-loader" id="modal-' + loadPrefix + '">' +
                                        '<div class="modal-dialog">' +
                                            '<div class="modal-content">' +
                                                '<div class="modal-body">' +
                                                    '<h2 id="modal-title-' + loadPrefix + '"></h2>' +
                                                    '<p class="modal-text"><i class="fa fa-circle-o-notch fa-spin"></i> <span id="modal-text-' + loadPrefix + '"></span></p>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>';

                $("#" + loadTag).append(ajaxLoader);
                if (title){
                    $("#modal-title-" + loadPrefix).text(title);
                }
                if (text){
                    $("#modal-text-" + loadPrefix).text(text);
                }

                $("#modal-" + loadPrefix).modal({
                    keyboard: keyboard,
                    backdrop: backdrop
                });
            },
            closeAjaxLoader : function (loadPrefix){
                $("#modal-" + loadPrefix).modal('hide');
            },
           confirmBox : function (loadPrefix, loadTag, title, text, backdrop, keyboard, confirm, close){
               var confirmBox = '<div class="modal fade ajax-loader" id="confirmBox-' + loadPrefix + '">' +
                                   '<div class="modal-dialog">' +
                                       '<div class="modal-content">' +
                                               '<div class="modal-body">' +
                                                    '<h2 id="modal-title-' + loadPrefix + '"></h2>' +
                                                    '<p class="modal-text"><span id="modal-text-' + loadPrefix + '"></span></p>' +
                                                    '<div class="modal-controls">' +
                                                        '<button class="btn btn-link btn-close" id="btnCloseConfirm-' + loadPrefix + '">Cancel</button>' +
                                                        '<button class="btn btn-primary btn-confirm" id="btnConfirm-' + loadPrefix + '">Confirm</button>' +
                                                    '</div>'
                                               '</div>' +
                                           '</div>' +
                                       '</div>' +
                                   '</div>';

               $("#" + loadTag).append(confirmBox);

               if (title){
                   $("#modal-title-" + loadPrefix).text(title);
               }

               if (text){
                   $("#modal-text-" + loadPrefix).text(text);
               }

               $("#confirmBox-" + loadPrefix).modal({
                   keyboard: keyboard,
                   backdrop: backdrop
               });

               $("#btnConfirm-" + loadPrefix).click(function(){
                    confirm();
               });
               $("#btnCloseConfirm-" + loadPrefix).click(function(){
                    close();
               });
           },
            closeConfirmBox : function (loadPrefix){
                $("#confirmBox-" + loadPrefix).modal('hide');
            }
        };
    });