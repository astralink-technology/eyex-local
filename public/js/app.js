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
    , 'eyexApp.indexController'
    , 'eyexApp.accountSettingsController'
    , 'eyexApp.announcementsController'
    , 'eyexApp.changePasswordController'
    , 'eyexApp.changePinController'
    , 'eyexApp.companyAccountController'
    , 'eyexApp.companyController'
    , 'eyexApp.companyPasswordController'
    , 'eyexApp.completeInvitationController'
    , 'eyexApp.editCompanyProfileController'
    , 'eyexApp.editProfileController'
    , 'eyexApp.employeesController'
    , 'eyexApp.extensionsController'
    , 'eyexApp.galleryController'
    , 'eyexApp.ivrsController'
    , 'eyexApp.profileController'
    , 'eyexApp.sipSettingsController'
    , 'eyexApp.activitiesController'
    , 'eyexApp.devicesController'
    , 'eyexApp.setupController'
    , 'eyexApp.cardAccessController'
    , 'eyexApp.doorAccessController'
    , 'eyexApp.cloudAccessibilityController'
    , 'eyexApp.personalExtensionController'
]).
    config(function ($routeProvider, $locationProvider) {
        $locationProvider.html5Mode(true);

    }).
    run(['$rootScope', '$http', '$sce', function($rootScope, $http, $sce){
    }]);
