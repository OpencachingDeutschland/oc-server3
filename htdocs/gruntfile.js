module.exports = function (grunt) {
    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);

    grunt.initConfig({
        dirs: {
            source: 'app/Resources/assets',
            destination: 'web/assets'
        },

        clean: {
            all: '<%= dirs.destination %>',
            css: '<%= dirs.destination %>/css/',
            js: '<%= dirs.destination %>/js/'
        },
        scsslint: {
            development: [
                '<%= dirs.source %>/scss/**/*'
            ],
            options: {
                config: '.scss-lint.yml',
                reporterOutput: 'scss-lint-report.xml',
                colorizeOutput: true
            }
        },
        sass: {
            production: {
                options: {
                    style: 'compressed'
                },
                files: {
                    '<%= dirs.destination %>/css/style.min.css': '<%= dirs.source %>/scss/all.scss',
                    '<%= dirs.destination %>/css/legacy.min.css': '<%= dirs.source %>/scss/legacy.scss'
                }
            },
            development: {
                options: {
                    sourceMap: true,
                    style: 'expanded'
                },
                files: {
                    '<%= dirs.destination %>/css/style.min.css': '<%= dirs.source %>/scss/all.scss',
                    '<%= dirs.destination %>/css/legacy.min.css': '<%= dirs.source %>/scss/legacy.scss'
                }
            },
            options: {
                importer: require('grunt-sass-tilde-importer'),
                includePaths: [
                    '<%= dirs.source %>/scss/'
                ]
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    '<%= dirs.destination %>/css/style.min.css': [
                        '<%= dirs.destination %>/css/style.min.css'
                    ],
                    '<%= dirs.destination %>/css/legacy.min.css': [
                        '<%= dirs.destination %>/css/legacy.min.css'
                    ]
                }
            }
        },
        uglify: {
            production: {
                options: {
                    mangleProperties: false,
                    reserveDOMProperties: true
                },
                files: {
                    '<%= dirs.destination %>/js/main.js': [
                        '<%= dirs.source %>/js/**/*.js'
                    ]
                }
            },
            development: {
                options: {
                    sourceMap: true,
                    beautify: true,
                    mangle: false,
                    compress: false
                },
            },
        },
        watch: {
            css: {
                files: [
                    '<%= dirs.source %>/scss/**/*.scss'
                ],
                tasks: [
                    'development:css'
                ],
                options: {
                    spawn: false
                }
            },
            scripts: {
                files: [
                    '<%= dirs.source %>/js/**/*.js'
                ],
                tasks: [
                    'development:js'
                ]
            },
            options: {
                atBegin: true
            }
        }
    });


    grunt.registerTask('default', [
        'build'
    ]);

    //Builds css&js once for development
    grunt.registerTask('build', [
        'clean:all',
        'uglify',
        'sass:development'
    ]);

    grunt.registerTask('development:css', [
        'scsslint:development',
        'clean:css',
        'sass:development'
    ]);

    grunt.registerTask('development:js', [
        'clean:js',
        'uglify:development'
    ]);

    grunt.registerTask('development', [
        'development:js',
        'development:css'
    ]);

    //Builds css&js once for production
    grunt.registerTask('production', [
        'clean:all',
        'uglify:production',
        'sass:production',
        'cssmin'
    ]);

    grunt.task.renameTask('chokidar', 'watch');
};
