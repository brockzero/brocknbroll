'use strict';

/* Controllers */
/*
var wordsControllers = angular.module('wordsControllers', []);

WordsControllers.controller('wordsListCtrl', ['$scope','words',
  function($scope, Words) {
    //$scope.comics = Comic.query();
    //$scope.orderProp = 'index';
  }]);

comicControllers.controller('ComicDetailCtrl', ['$scope', '$routeParams','$filter','Comic','$location',
  function($scope, $routeParams, $filter, Comic, $location) {

  //   // Simple GET request example :
  // $http.get('controllers/controllers\ComicController.php?page=1').
  //   success(function(data, status, headers, config) {
  //     // this callback will be called asynchronously
  //     // when the response is available
  //   }).
  //   error(function(data, status, headers, config) {
  //     // called asynchronously if an error occurs
  //     // or server returns response with an error status.
  //   });
    //http://stackoverflow.com/questions/2269307/using-jquery-ajax-to-call-a-php-function
    //console.log($routeParams);
    var comics = Comic.get({page: $routeParams.comicId, action:'Display'})
    .$promise.then(function(comics) {
      console.log(comics);
      $scope.comic = comics;
      $scope.nextComic = parseInt($routeParams.comicId) < comics.pagingLast ? parseInt($routeParams.comicId)+1 : comics.pagingLast;
      $scope.prevComic = parseInt($routeParams.comicId) > 1 ? parseInt($routeParams.comicId)-1 : 1;
      $scope.serverAbsUrl = $location.absUrl();
      $scope.serverHost = "http://"+ $location.host();
    });
  }]);

comicControllers.controller('ComicRandomCtrl', ['$scope', '$routeParams', '$location','Comic',
  function($scope, $routeParams, $location, Comic) {
    var comics = Comic.get({page: $routeParams.comicId, action:'Display'})
    .$promise.then(function(comics) {
      console.log(comics);
      var comicId = Math.ceil(Math.random()*comics.pagingLast);
      $location.path("/comics/" + comicId);
    });
  }]);
*/
