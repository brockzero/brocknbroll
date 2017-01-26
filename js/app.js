'use strict';

  var brocknbrollApp = angular.module('brocknbrollApp', [
    'ngRoute',
    //'comicAnimations',
    'comicController',
    //'comicFilters',
    'comicService',
    'ngSanitize'
  ]);

  brocknbrollApp.config(['$routeProvider','$locationProvider',
    function($routeProvider, $locationProvider) {
      $routeProvider.
        when('/comics', {
          templateUrl: 'comic/views/comic-detail.html',
          controller: 'ComicDetailCtrl'
        }).
        when('/comics/random', {
          templateUrl: 'comic/views/comic-detail.html',
          controller: 'ComicRandomCtrl'
        }).
        when('/comics/:comicId', {
          templateUrl: 'comic/views/comic-detail.html',
          controller: 'ComicDetailCtrl'
        }).
        when('/archive/', {
          templateUrl: 'comic/views/comic-list.html',
          controller: 'ComicListCtrl'
        }).
        otherwise({
          redirectTo: '/comics'
        });
        // use the HTML5 History API, otherwise you have to use server side configuration
        //https://docs.angularjs.org/guide/$location
        //to accomplish html5 routing and the ability to refresh
        // $locationProvider.html5Mode(true);
    }]);
