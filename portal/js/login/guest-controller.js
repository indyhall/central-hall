// Login / Guest Controller
angular.module('app').controller('LoginGuestController', function($scope, AuthService) {
	$scope.errorMessage = false;

	$scope.name = '';
	$scope.password = '';
	$scope.host = '';

	var login = AuthService.buildLoginAction(guestLoginUrl, $scope);
	$scope.login = function() {
		return login({
			name: $scope.name,
			password: $scope.password,
			host: $scope.host,
			mac_address: $('#mac-address').val()
		});
	};
});