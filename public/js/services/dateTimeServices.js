'use strict';

angular.module('eyexApp.dateTimeServices', []).
    factory('dateTimeServices', ['stringServices', function (stringServices) {
    return {
        //add zeroes
        hourPadLeft: function (string) {
            if (string < 10){
                return '0' + string;
            }else{
                return string;
            }
        },
        timeSince : function (date){
            var now = moment();
            var secondDiff = now.diff(date, 'seconds');
            var minDiff = now.diff(date, 'minutes');
            var hourDiff = now.diff(date, 'hours');
            var dayDiff = now.diff(date, 'days');
            var weekDiff = now.diff(date, 'weeks');
            var monthDiff = now.diff(date, 'months');

            var diffUnit; //default is seconds
            var diffDescription = '';
            if (monthDiff > 1){
                diffDescription = 'On ' + moment(date).format('ddd MMM YYYY, h:mm a');
            }else if (monthDiff){
                diffUnit = 'month';
                diffDescription = monthDiff + ' ' + stringServices.pluralize(monthDiff, diffUnit) +  ' ago on ' + moment(date).format('DD MMM YYYY h:mma');
            }else if (weekDiff){
                diffUnit = 'week';
                diffDescription = weekDiff + ' ' +  stringServices.pluralize(weekDiff, diffUnit) +  ' ago on ' + moment(date).format('DD MMM YYYY h:mma');
            }else if (dayDiff){
                diffUnit = 'day';
                diffDescription = dayDiff + ' ' +  stringServices.pluralize(dayDiff, diffUnit) +  ' ago on ' + moment(date).format('DD MMM YYYY h:mma');
            }else if (hourDiff){
                diffUnit = 'hour';
                diffDescription = hourDiff + ' ' +  stringServices.pluralize(hourDiff, diffUnit) +  ' ago at ' + moment(date).format('h:mma')
            }else if (minDiff){
                diffUnit = 'min';
                diffDescription = minDiff + ' ' +  stringServices.pluralize(minDiff, diffUnit) +  ' ago';
            }else if (secondDiff){
                diffUnit = 's';
                diffDescription = secondDiff + diffUnit +  ' ago';
            }

            return diffDescription;
        }
    };

}]);