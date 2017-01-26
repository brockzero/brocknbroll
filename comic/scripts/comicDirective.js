'use strict';

/* Directives */
// comicController is defined in comicController.js
comicController.directive('comicDetail', function() {
    return {
      template: '<span><img ng-src="comic/content/{{comic.fileName}}" ng-attr-title="{{comic.titleAttr}}" class="comic"></span>'
    };
  });

comicController.directive('comicNav', function() {
    return {
      templateUrl: 'comic/views/comic-nav.html'
    };
  });
