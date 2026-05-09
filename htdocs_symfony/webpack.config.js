const path = require('path');

/**
 *
 * @type {Encore}
 */
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

.addEntry('shared', './assets/shared.js')
.addEntry('oc-style', './assets/app/oc-style.js')
.addEntry('bs4', './assets/bs4/bs4.js')
.addEntry('backend', './assets/backend/backend.js')

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

// enables Loaders
.enableSassLoader((options) => {
    options.api = 'modern-compiler';
    options.sassOptions = {
        loadPaths: [
            path.resolve(__dirname, 'node_modules')
        ],
        // Blendet Warnungen aus Bibliotheken (node_modules) aus
        quietDeps: true,
        // Schaltet die spezifischen neuen Meldungen von Dart Sass 2.x stumm
        silenceDeprecations: [
            // 'global-builtin', // Behebt die Meldung zu mix()
            // 'import',         // Behebt Meldungen zum veralteten @import
            // 'color-functions', // Behebt Meldungen zu lighten/darken
            // 'mixed-decls'      // Behebt Meldungen zu CSS-Deklarationen
        ],
    };
})
.enablePostCssLoader()

// https://symfony.com/doc/current/frontend/encore/copy-files.html#referencing-image-files-from-a-template
.copyFiles({
    from: './assets/app/images',

    // optional target path, relative to the output dir
    to: 'images/[path][name].[hash:8].[ext]',

    // only copy files matching this pattern
    pattern: /\.(png|jpg|jpeg)$/
})

.copyFiles({
    from: './assets/backend/images',

    // optional target path, relative to the output dir
    to: 'images/[path][name].[hash:8].[ext]',

    // only copy files matching this pattern
    pattern: /\.(png|jpg|jpeg)$/
})

.enableIntegrityHashes(Encore.isProduction())

// .autoProvidejQuery() - Removed to eliminate jQuery dependency

.addPlugin(new StylelintPlugin({
    // Behebt einfache Fehler (wie Einrückungen) automatisch beim Speichern
    fix: true,

    // Hilft Stylelint 17, die neue Konfigurationsdatei sicher zu finden
    configFile: path.resolve(__dirname, '.stylelintrc.mjs'),

    // WICHTIG: Erzwingt den SCSS-Parser (verhindert "Unknown word" Fehler)
    customSyntax: 'postcss-scss',

    // Sucht nur im assets-Pfad nach Stylesheets (beschleunigt den Build)
    context: 'assets',
    files: '**/*.scss',

    // Pfade ausschliessen
    exclude: ['node_modules', 'vendor', 'public'],

    // Zeigt Fehler im Browser-Overlay von Symfony an
    emitError: true,
    failOnError: Encore.isProduction(),
}))
;

module.exports = Encore.getWebpackConfig();
