#!/usr/bin/php
<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

/*  refresh cached files and translations
 *
 *  stop/start apache
 *  delete/recreate cached files
 */

if (!isset($opt['rootpath'])) {
    $opt['rootpath'] = __DIR__ . '/../htdocs/';
}
require_once __DIR__ . '/../htdocs/lib2/cli.inc.php';

$webCache = new OcLegacy\Cache\WebCache();

if ($argc !== 2 || $argv[1] !== 'pass2') {
    echo "delete cached files\n";
    $webCache->clearCache();

    echo "clearing symfony caches\n";
    system('php ' . __DIR__ . '/../htdocs/bin/console cache:clear');

    echo "create translation files for gettext()\n";
    $translationHandler->createMessageFiles();

    // After recreation of the message files (.mo files), the translation
    // system needs to be re-initialized. We do this by running the rest
    // of the script in a separate php process. This fixes issue #807.

    system('php ' . __DIR__ . '/clear-webcache.php pass2');
} else {
    echo "create menu cache file\n";
    $webCache->createMenuCache();

    echo "create label cache file\n";
    $webCache->createLabelCache();

    echo "Precompiling template files\n";

    $webCache->preCompileAllTemplates(); // TODO We need to fix this in DEV Environment, to prevent problems with chmod
}
