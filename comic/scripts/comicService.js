'use strict';

/* Services */

  var comicService = angular.module('comicService', ['ngResource']);
  //http://stackoverflow.com/questions/16194203/conditionally-load-external-json-into-localstorage-as-string
  //for use with breeze?
  //http://www.getbreezenow.com/documentation/querying-locally
  // comicServices.factory('Comic', ['$resource',
  //   function($resource){
  //     return $resource('imgs.xkcd.com/comics.json', {}, {
  //       query: {method:'GET', isArray:true}
  //     });
  //   }]);

  //http://www.angularcode.com/demo-of-a-simple-crud-restful-php-service-used-with-angularjs-and-mysql/
    comicService.factory('Comic', ['$resource',
      function($resource){
        return $resource('http://brocknbroll.tk/comic/comic.php');
      }]);
