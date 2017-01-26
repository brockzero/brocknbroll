'use strict';

/* Controllers */

var comicController = angular.module('comicController', []);

comicController.controller('ComicListCtrl', ['$scope','Comic',
  function($scope, Comic) {
    $scope.comics = Comic.query({action:'archive'});
    console.log($scope.comics);
  }]);

comicController.controller('ComicDetailCtrl', ['$scope', '$routeParams','$filter','Comic','$location',
  function($scope, $routeParams, $filter, Comic, $location) {
    var comics = Comic.get({page: $routeParams.comicId, action:'Display'})
    .$promise.then(function(comics) {
      $scope.comic = comics;
      $scope.nextComic = parseInt($routeParams.comicId) < comics.pagingLast ? parseInt($routeParams.comicId)+1 : comics.pagingLast;
      $scope.prevComic = parseInt($routeParams.comicId) > 1 ? parseInt($routeParams.comicId)-1 : 1;
      $scope.serverAbsUrl = $location.absUrl();
      $scope.serverHost = "http://"+ $location.host();
    });
  }]);

comicController.controller('ComicRandomCtrl', ['$scope', '$routeParams', '$location','Comic',
  function($scope, $routeParams, $location, Comic) {
    var comics = Comic.get({page: $routeParams.comicId, action:'Display'})
    .$promise.then(function(comics) {
      var comicId = Math.ceil(Math.random()*comics.pagingLast);
      $location.path("/comics/" + comicId);
    });
  }]);
