'use strict';
var sigmaVariablesAnalysis = function(a) {
    var r = {mean: 0, variance: 0, deviation: 0}, t = a.length;
    for(var m, s = 0, l = t; l--; s += a[l]);
    for(m = r.mean = s / t, l = t, s = 0; l--; s += Math.pow(a[l] - m, 2));
    return r.deviation = Math.sqrt(r.variance = s / t), r;
}

var withinSigma = function(sigmaVars , val, stdev) {
        var low = sigmaVars.mean - (stdev * sigmaVars.deviation);
        var hi = sigmaVars.mean + (stdev * sigmaVars.deviation);
        return (val > low) && (val < hi);
    }

angular.module('eyexApp.statisticsServices', []).
    factory('statisticsServices', ['$http', 'dateTimeServices', function ($http, dateTimeServices) {
    return {
        //add zeroes
        medianTime: function (values) {
            if (values.length > 1){
                values.sort();
                var half = Math.floor(values.length/2);
                if(values.length % 2){
                    var hour = parseInt(values[half].substring(0, values[half].indexOf(":")));
                    var minutes = values[half].substring(values[half].indexOf(":") + 1);
                    if (hour > 23){
                        hour -= 24;
                        return dateTimeServices.hourPadLeft(hour) + ":" + minutes;
                    }else{
                        return values[half];
                    }
                } else {
                    var hour = parseInt(values[half-1].substring(0, values[half-1].indexOf(":")));
                    var minutes = values[half-1].substring(values[half-1].indexOf(":") + 1);
                    if (hour > 23){
                        hour -= 24;
                        return dateTimeServices.hourPadLeft(hour) + ":" + minutes;
                    }else{
                        return(values[half-1]);
                    }
                }
            }else if (values.length == 1){
                return values[0];
            }else{
                return null;
            }
        },
        median: function(values, wholeNumber){
            values.sort( function(a,b) {return a - b;} );

            var half = Math.floor(values.length/2);

            if(values.length % 2){
                return values[half];
            }else{
                if (wholeNumber){
                    return Math.floor((values[half-1] + values[half]) / 2.0);
                }else{
                    return (values[half-1] + values[half]) / 2.0;
                }
            }
        },
        mode : function(array){
            if(array.length == 0)
                return null;
            var modeMap = {};
            var maxEl = array[0], maxCount = 1;
            for(var i = 0; i < array.length; i++)
            {
                var el = array[i];
                if(modeMap[el] == null)
                    modeMap[el] = 1;
                else
                    modeMap[el]++;
                if(modeMap[el] > maxCount)
                {
                    maxEl = el;
                    maxCount = modeMap[el];
                }
            }
            return maxEl;
        },
        modeArrays : function(array)
        {
            if (array.length == 0)
                return null;
            var modeMap = {},
                maxCount = 1,
                modes = [array[0]];

            for(var i = 0; i < array.length; i++)
            {
                var el = array[i];

                if (modeMap[el] == null)
                    modeMap[el] = 1;
                else
                    modeMap[el]++;

                if (modeMap[el] > maxCount)
                {
                    modes = [el];
                    maxCount = modeMap[el];
                }
                else if (modeMap[el] == maxCount)
                {
                    modes.push(el);
                    maxCount = modeMap[el];
                }
            }
            return modes;
        },
        sigmaArrange : function (values, sigmaCount){
            var sigmaVars = sigmaVariablesAnalysis(values);
            var arranged = new Array();
            var outliers = new Array();

            for(var i = 0; i < values.length; i++) {
                if (withinSigma(sigmaVars, values[i], sigmaCount)){
                    arranged.push(values[i]);
                }else{
                    outliers.push(values[i]);
                }
            }

            var sigmaObject = new Object();
            sigmaObject.arranged = arranged;
            sigmaObject.outliers = outliers;

            return sigmaObject;
        }
    };

}]);