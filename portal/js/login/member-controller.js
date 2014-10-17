// Login / Member Controller
angular.module('app').controller('LoginMemberController', function($scope, AuthService) {
	$scope.errorMessage = false;

	$scope.username = '';
	$scope.password = '';
	$scope.device_name = 'My ' + guessDevice();

	var login = AuthService.buildLoginAction(loginUrl, $scope);
	$scope.login = function() {
		return login({
			username: $scope.username,
			password: $scope.password,
			device_name: $scope.deviceName,
			mac_address: $('#mac-address').val()
		});
	};

	function guessDevice() {
		var ua = navigator.userAgent;

		if (ua.match(/Windows Phone/i)) { // Windows Phone can pretend to be iPhone, so check that first
			return 'Windows Phone';
		} else if (ua.match(/Android/i)) { // Android devices may do the same, so check them next
			if (ua.match('/KFOTE? Build/i')) { // Kindle Fire has KFOT or KFOTE in it, I think
				return 'Kindle Fire';
			}
			return 'Android Device';
		} else if (ua.match(/iPad/i)) { // iPad first, 'cause it has iPhone in the UA
			return 'iPad';
		} else if (ua.match(/iPhone/i)) { // iPhone before Mac because iPhone says "like Mac"
			return 'iPhone';
		} else if (ua.match(/Mac OS X/i)) {
			return 'Mac';
		} else if (ua.match(/Windows/i)) {
			return 'Windows PC';
		} else if (ua.match(/Mobile/i)) {
			return 'Mobile Device';
		}

		return '';
	}
});