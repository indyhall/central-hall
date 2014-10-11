'use strict';

/**
 * @ngdoc function
 * @name centralHallApp.controller:AboutCtrl
 * @description
 * # AboutCtrl
 * Controller of the centralHallApp
 */
angular.module('centralHallApp')
  .controller('AboutCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });
