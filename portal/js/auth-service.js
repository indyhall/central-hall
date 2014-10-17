// Authentication Service
angular.module('app').factory('AuthService', function($http) {
	return {
		buildLoginAction: function(baseUrl, $scope) {
			return function(params) {
				var url = baseUrl + '&callback=JSON_CALLBACK';
				var cb = function(data) {
					if (data.ok && true === data.ok) {
						// Success
						$('#auth-form').submit();
						return;
					}

					if (!data.message) {
						data.message = 'Unknown error.  Please try again and find a staff member for help if you continue ' +
							'to see this message.';
					}

					$('form :input').attr('readonly', false);
					$('form .btn.fade').addClass('in');
					$scope.errorMessage = data.message;
					$('#login').addClass('animated shake');
				};

				$('form :input').attr('readonly', true);
				$('form .btn.fade').removeClass('in');
				$('#login').removeClass('animated shake');
				$scope.errorMessage = false;

				$http.jsonp(url, { params: params }).success(cb).error(function(data, status, headers, config) {
					cb({
						ok: false,
						message: 'Unable to login due to a network error.  Please try again and find a staff member for help ' +
							'if you continue to see this message.',
						data: {
							data: data,
							status: status,
							headers: headers,
							config: config
						}
					});
				});
			};
		}
	};
});