module.exports = function (grunt) {
	var path = require('path');
	var jit = require('jit-grunt');

	// Just-in-time loading of tasks
	jit(grunt, {
		useminPrepare: 'grunt-usemin',
		ngtemplates: 'grunt-angular-templates'
	});

	// Config
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		connect: {
			dev: {
				options: {
					port: 60065,
					hostname: 'localhost',
					base: ['.'],
					open: 'http://localhost:60065/portal/index.html',
					keepalive: true
				}
			},
			build: {
				options: {
					port: 60065,
					hostname: 'localhost',
					base: ['.'],
					open: 'http://localhost:60065/portal/build/index.html',
					keepalive: true
				}
			}
		},
		clean: {
			build: ['portal/build/'],
			tmp: ['.tmp/']
		},
		copy: {
			freshportal: {
				expand: true,
				cwd: 'portal/',
				src: [
					'**/*',
					'!build',
					'!build/**/*'
				],
				dest: '.tmp/'
			},
			html: {
				src: '.tmp/index.html',
				dest: 'portal/build/index.html'
			}
		},
		useminPrepare: {
			html: '.tmp/index.html',
			options: {
				dest: 'portal/build',
				staging: '.tmp/.usemin/',
				flow: {
					steps: {
						'js': ['concat', 'uglifyjs'],
						'css': ['concat', 'cssmin'],
						'less': [{
							name: 'less',
							createConfig: function (context, block) {
								var cfg = { files: [] },
									filesDef = {};

								filesDef.dest = path.join(context.outDir, block.dest);
								filesDef.src = [];

								context.inFiles.forEach(function (inFile) {
									filesDef.src.push(path.join(context.inDir, inFile));
								});

								cfg.files.push(filesDef);
								context.outFiles = [block.dest];
								return cfg;
							}
						}]
					},
					post: {}
				}
			}
		},
		ngAnnotate: {
			options: {
				singleQuotes: true,
				add: true
			},
			portal: {
				files: [{
					expand: true,
					src: '.tmp/js/**/*.js'
				}]
			}
		},
		ngtemplates: {
			app: {
				cwd: 'portal/',
				src: 'partials/**/*.html',
				dest: '.tmp/js/templates.js',
				options:  {
					usemin: 'js/app.js'
				}
			}
		},
		dom_munger: {
			html: {
				options: {
					remove: '.dev-only'
				},
				src: 'portal/build/index.html'
			}
		},
		autoprefixer: {
			options: {
				browsers: ['last 3 versions', 'ie 7', 'ie 8', 'ie 9']
			},
			build: {
				src: 'portal/build/css/app.css'
			}
		},
		cssmin: {
			build: {
				files: {'portal/build/css/app.css': 'portal/build/css/app.css'}
			}
		},
		filerev: {
			options: {
				algorithm: 'md5',
				length: 5
			},
			build: {
				src: 'portal/build/**/*.{css,js,png}'
			}
		},
		usemin: {
			html: ['portal/build/index.html'],
			css: ['portal/build/styles/app.css'],
			options: {
				assetsDirs: ['portal/build/'],
				blockReplacements: {
					less: function (block) {
						return '<link rel="stylesheet" href="' + block.dest + '" />';
					}
				}
			}
		}
	});

	// Aliases
	grunt.registerTask('default', ['connect:dev']);
	grunt.registerTask('serve', ['connect:dev']);
	grunt.registerTask('servebuild', ['connect:build']);
	grunt.registerTask('build', [
		'clean',                // Clean build and temp dirs
		'copy:freshportal',     // Make a working copy of portal code
		'ngAnnotate:portal',    // Prepare Angular files for minification
		'useminPrepare',        // Prepare for usemin
		'ngtemplates',          // Compile Angular templates
		'copy:html',            // Copy HTML to build dir
		'concat:generated',     // Run generated tasks
		'uglify:generated',     // ..
		'less:generated',       // ..
		'autoprefixer',         // Add prefixes to CSS
		'cssmin',               // Minify CSS
		'filerev',              // Revision assets
		'usemin',               // Apply to HTML
		'dom_munger'            // Final modifications to HTML
	]);
};