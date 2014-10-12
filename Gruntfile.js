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
		usemin: {
			html: 'portal/index.html',
			options: {
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
		'useminPrepare',
		'concat:generated',
		'uglify:generated',
		'less:generated',
		// 'filerev',
		'usemin'
	]);

};