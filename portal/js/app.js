// Setup App
angular.module('app', ['ui.router'])
	.config(function($stateProvider, $urlRouterProvider) {
		$urlRouterProvider
			.otherwise('/login/members');
		$stateProvider
			.state('login', {
				url: '/login',
				templateUrl: 'partials/login.html',
				controller: 'LoginController'
			})
			.state('login.members', {
				url: '/members',
				templateUrl: 'partials/login/members.html'
			})
			.state('login.guests', {
				url: '/guests',
				templateUrl: 'partials/login/guests.html'
			})
			.state('login.help', {
				url: '/help',
				templateUrl: 'partials/login/help.html'
			});
});;