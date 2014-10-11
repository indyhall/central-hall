'use strict';

/**
 * @ngdoc function
 * @name centralHallApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the centralHallApp
 */
angular.module('centralHallApp')
  .controller('MainCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });
