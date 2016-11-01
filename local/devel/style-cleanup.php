<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *  This tool currently fixes the following code style issues:
 *    - resolve tabs to 4-char-columns
 *    - remove trailing whitespaces
 *    - set line ends to LF(-only)
 *    - remove ?> and blank lines at end of file
 *    - add missing LF to end of file
 *  It also warns on the following issues:
 *    - characters before open tag at start of file
 *    - short open tags
 *  This script may be run any time to check and clean up the current OC code.
 *  Unicode Reminder メモ
 ***************************************************************************/

# The following code is made to run also on the developer host, which may
# have a restricted environment like an old Windows PHP. Keep it simple and
# do not include other OC code.

use Oc\Util\StyleCleanUp;

require_once __DIR__ . '/../../htdocs/vendor/autoload.php';

$exclude = [
    'htdocs/cache',
    'htdocs/cache2',
    'htdocs/lib2/smarty',
    'htdocs/okapi',
    'htdocs/templates2/mail',
    'htdocs/var',
    'htdocs/vendor',
];

chdir(__DIR__ . '/../..');

$cleanup = new StyleCleanUp();
$cleanup->run('.', $exclude);

echo $cleanup->getLinesModified() . ' lines in ' . $cleanup->getFilesModified() . ' files' . " have been cleaned up\n";
