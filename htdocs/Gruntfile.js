module.exports = function(grunt) {
    grunt.initConfig({
        sass: {
            dist: {
                files: {
                    'web/css/style.css': 'theme/scss/all.scss'
                }
            },
            options: {
                sourceMap: true
            }
        },
        watch: {
            source: {
                files: ['theme/scss/all.scss', 'theme/scss/components/*.scss'],
                tasks: ['sass']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');
    grunt.registerTask('default', ['sass']);
};