// Loads JS files from the server w/ cache busting
function fe(a, cb) {
    var key;
    for (key in a) {
        if (a.hasOwnProperty(key)  &&        // These are explained
            /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
            key <= 4294967294                // away below
            ) {
            cb(a[key]);
        }
    }
}

var scriptTag = document.getElementsByTagName('script')[0];

fe(files.css, function(file) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = base + file;
    scriptTag.parentNode.insertBefore(link, scriptTag);
});

fe(files.js, function(file) {
    var script = document.createElement('script');
    script.async = 1;
    script.src = base + file;
    scriptTag.parentNode.insertBefore(script, scriptTag);
});