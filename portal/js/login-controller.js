// Login Controller
angular.module('app').controller('LoginController', function($scope, $state) {
	$state.transitionTo('login.members');
});