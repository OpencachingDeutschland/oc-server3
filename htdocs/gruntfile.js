module.exports = function (grunt) {
    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);

    var sourcePath = 'theme/frontend';

    var vendorJsFiles = [
        '<%= dirs.composer %>/components/jquery/jquery.min.js'
    ];
    var ownJsFiles = grunt.file.readJSON('theme/frontend/js/files.json').files;

    ownJsFiles = ownJsFiles.map(function(file) {
        return sourcePath + '/js/' + file;
    });

    var jsFiles = vendorJsFiles.concat(ownJsFiles);

    grunt.initConfig({
        dirs: {
            composer: 'vendor',
            source: sourcePath,
            destination: 'web/assets'
        },

        clean: {
            all: '<%= dirs.destination %>',
            css: '<%= dirs.destination %>/css/',
            js: '<%= dirs.destination %>/js/',
            images: '<%= dirs.destination %>/images/',
            fonts: '<%= dirs.destination %>/fonts/',
            vendor: '<%= dirs.destination %>/vendor/'
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
        copyto: {
            images: {
                files: [
                    {
                        cwd: '<%= dirs.source %>/images/',
                        src: ['**/*'],
                        dest: '<%= dirs.destination %>/images/',
                        expand: true
                    }
                ]
            },
            fonts: {
                files: [
                    {
                        cwd: '<%= dirs.source %>/fonts/',
                        src: ['**/*'],
                        dest: '<%= dirs.destination %>/fonts/',
                        expand: true
                    }
                ]
            },
            vendor: {
                files: [
                    {
                        cwd: '<%= dirs.source %>/vendor/',
                        src: ['**/*'],
                        dest: '<%= dirs.destination %>/vendor/',
                        expand: true
                    }
                ]
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
                    '<%= dirs.destination %>/js/main.js': jsFiles
                }
            },
            development: {
                options: {
                    sourceMap: true,
                    beautify: true,
                    mangle: false,
                    compress: false
                },
                files: {
                    '<%= dirs.destination %>/js/main.js': jsFiles
                }
            },
        },
        watch: {
            images: {
                files: [
                    '<%= dirs.source %>/images/**/*'
                ],
                tasks: [
                    'copyto:images'
                ]
            },
            fonts: {
                files: [
                    '<%= dirs.source %>/fonts/**/*'
                ],
                tasks: [
                    'copyto:fonts'
                ]
            },
            fonts: {
                files: [
                    '<%= dirs.source %>/vendor/**/*'
                ],
                tasks: [
                    'copyto:vendor'
                ]
            },
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
        'sass:development',
        'copyto:images',
        'copyto:fonts'
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
        'development:css',
        'copyto:images',
        'copyto:fonts'
    ]);

    //Builds css&js once for production
    grunt.registerTask('production', [
        'clean:all',
        'uglify:production',
        'sass:production',
        'cssmin',
        'copyto:images',
        'copyto:fonts'
    ]);

    grunt.task.renameTask('chokidar', 'watch');
};
