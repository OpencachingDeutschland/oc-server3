const Encore = require('@symfony/webpack-encore');
const StylelintPlugin = require('stylelint-webpack-plugin');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/app.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enablePostCssLoader()

    // https://symfony.com/doc/current/frontend/encore/copy-files.html#referencing-image-files-from-a-template
    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg)$/
    })

    .enableIntegrityHashes(Encore.isProduction())

    .autoProvidejQuery()

    .addPlugin(new StylelintPlugin({
        fix: true
    }))
;

module.exports = Encore.getWebpackConfig();
