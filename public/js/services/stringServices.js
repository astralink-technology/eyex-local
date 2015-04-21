'use strict';

angular.module('eyexApp.stringServices', []).
    factory('stringServices', function () {
    return {
        pluralize: function (count, noun) {
            var englishTeacher = noun.substr(noun.length - 1);
            var strictWords = [
                'day'
            ];

            var isStrict = false;
            if( $.inArray(noun, strictWords) != -1 ){
                isStrict = true;
            }
            if (noun){
                if (count > 1){
                    if (!isStrict){
                        if (englishTeacher == 'f'){
                            var pos = noun.lastIndexOf('f');
                            noun = noun.substring(0,pos) + 'ves'
                        }else if (englishTeacher == 'y'){
                            var pos = noun.lastIndexOf('y');
                            noun = noun.substring(0,pos) + 'ies'
                        }else{
                            noun += 's';
                        }
                    }else{
                        noun += 's';
                    }
                    return noun;
                }else{
                    return noun;
                }
            }else{
                return noun;
            }
        },
        padFront : function (num, size){
            var padded = num + "";
            while (padded.length < size) padded = "0" + padded;
            return padded;
        }
    };

});