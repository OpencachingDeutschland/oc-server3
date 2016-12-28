#!/usr/bin/php
<?php
/***************************************************************************
 *  For license information see doc/license.txt
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

$webCache = new Oc\Cache\WebCache();

if ($argc !== 2 || $argv[1] !== 'pass2') {
    // stop apache
    system($opt['httpd']['stop']);

    echo "Delete cached files\n";
    $webCache->clearCache();

    echo "clearing symfony caches\n";
    system('php ' . __DIR__ . '/../htdocs/bin/console cache:clear');

    echo "Create translation files for gettext()\n";
    $translationHandler->createMessageFiles();

    // After recreation of the message files (.mo files), the translation
    // system needs to be re-initialized. We do this by running the rest
    // of the script in a separate php process. This fixes issue #807.

    system('php ' . __DIR__ . '/clear-webcache.php pass2');
} else {
    echo "Create menu cache file\n";
    $webCache->createMenuCache();

    echo "Create label cache file\n";
    $webCache->createLabelCache();

    echo "Precompiling template files\n";

    $webCache->preCompileAllTemplates(); #TODO We need to fix this in DEV Environment, to prevent problems with chmod

    // start apache
    system($opt['httpd']['start']);
}
