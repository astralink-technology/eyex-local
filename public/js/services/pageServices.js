'use strict';

angular.module('eyexApp.pageServices', []).
    factory('pageServices', function () {
        return {
            //ajaxLoader
            loadPager: function (dom, numberOfPages, currentPage) {

                var html = '<ul class="pagination">' +
                            '<li><a href="javascript:void(0)" id="prevPage"><i class="fa fa-chevron-left"></i></a></li>';

                for (var r = 0; r < numberOfPages; r ++){
                    var pageNo = r + 1;
                    if (pageNo == currentPage){
                        html += '<li class="active"><a href="javascript:void(0)" class="page-number" data-pagenum="' + pageNo + '">' + pageNo + '</a></li>';
                    }else{
                        html += '<li><a href="javascript:void(0)" class="page-number" data-pagenum="' + pageNo + '">' + pageNo + '</a></li>';
                    }
                }

                html += '<li><a href="javascript:void(0)" id="nextPage"><i class="fa fa-chevron-right"></i></a></li>';

                $(dom).html(html);
            }
        };
    });