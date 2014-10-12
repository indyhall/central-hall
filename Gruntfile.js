module.exports = function (grunt) {
	var path = require('path');
	var jit = require('jit-grunt');

	// Just-in-time loading of tasks
	jit(grunt, {
		useminPrepare: 'grunt-usemin'
	});

	// Config
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		connect: {
			server: {
				options: {
					port: 60065,
					hostname: 'localhost',
					base: ['.'],
					open: 'http://localhost:60065/portal/index.html',
					keepalive: true
				}
			}
		},
		clean: {
			build: ['portal/build/']
		},
		useminPrepare: {
			html: 'portal/index.html',
			options: {
				dest: 'portal/build',
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
		copy: {
			html: {
				src: 'portal/index.html',
				dest: 'portal/build/index.html'
			}
		},
		dom_munger: {
			html: {
				options: {
					remove: '.dev-only'
				},
				src: 'portal/build/index.html',
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
				files: {'portal/build/css/app.min.css': 'portal/build/css/app.css'}
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
	grunt.registerTask('default', ['connect:server']);
	grunt.registerTask('build', [
		'clean:build',
		'useminPrepare',
		'concat:generated',
		'uglify:generated',
		'less:generated',
		'copy:html',
		'autoprefixer',
		'cssmin',
		'filerev',
		'usemin',
		'dom_munger'
	]);

};