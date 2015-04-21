'use strict';

// Declare app level module which depends on filters, and services

angular.module('eyexApp', [
    'ngRoute'
    , 'ui.bootstrap'
    , 'duScroll'
    , 'eyexApp.validationServices'
    , 'eyexApp.dateTimeServices'
    , 'eyexApp.stringServices'
    , 'eyexApp.loadServices'
    , 'eyexApp.countryServices'
    , 'eyexApp.eyexServices'
    , 'eyexApp.paramsServices'
    , 'eyexApp.statisticsServices'
    , 'eyexApp.pageServices'
    , 'eyexApp.landingController'
]).
    config(function ($routeProvider, $locationProvider) {
        $locationProvider.html5Mode(true);

    }).
    run(['$rootScope', '$http', '$sce', function($rootScope, $http, $sce){
    }]);
