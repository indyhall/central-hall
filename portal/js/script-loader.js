// Loads JS files from the server w/ cache busting
(function() {
	var now = new Date();
	var url = '**SCRIPT_URL**' + now.getTime();
	var scriptTag = document.getElementsByTagName('script')[0];
	var scriptLoader = document.createElement('script');
	scriptLoader.async = 1;
	scriptLoader.src = url;
	scriptTag.parentNode.insertBefore(scriptLoader, scriptTag);
}());