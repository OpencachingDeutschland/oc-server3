<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/translate.class.php';
require_once __DIR__ . '/lib2/translationHandler.class.php';

/* config section
 */
global $msDirlist;
$msDirlist = [];
$msDirlist[] = '.';
$msDirlist[] = './config2';
$msDirlist[] = './lang/de';
$msDirlist[] = './lang/de/ocstyle';
$msDirlist[] = './lang/de/ocstyle/lib';
$msDirlist[] = './lib';
$msDirlist[] = './lib2';
$msDirlist[] = './lib2/logic';
$msDirlist[] = './lib2/search';
$msDirlist[] = './templates2/mail';
$msDirlist[] = './templates2/ocstyle';
$msDirlist[] = './util/notification';
$msDirlist[] = './util/watchlist';
$msDirlist[] = './util2/cron';
$msDirlist[] = './util2/cron/modules';

// recursively add directory trees to $msDirlist
addClassesDirectoriesToDirlist('src/Oc/Libse');

$transIdCols = [
    ['table' => 'attribute_categories', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'attribute_groups', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_attrib', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_attrib', 'text' => 'html_desc', 'trans_id' => 'html_desc_trans_id'],
    ['table' => 'cache_report_reasons', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_report_status', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_size', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_status', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_type', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'cache_type', 'text' => 'short2', 'trans_id' => 'short2_trans_id'],
    ['table' => 'coordinates_type', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'countries', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'languages', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'log_types', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'profile_options', 'text' => 'name', 'trans_id' => 'trans_id'],
    ['table' => 'statpics', 'text' => 'description', 'trans_id' => 'trans_id'],
    ['table' => 'sys_menu', 'text' => 'menustring', 'trans_id' => 'menustring_trans_id'],
    ['table' => 'sys_menu', 'text' => 'title', 'trans_id' => 'title_trans_id'],
    ['table' => 'towns', 'text' => 'name', 'trans_id' => 'trans_id'],
];

$tpl->name = 'translate';
$tpl->menuitem = MNU_ADMIN_TRANSLATE;

$login->verify();
$access = new translateAccess();

if (!$access->hasAccess()) {
    if ($login->admin & ADMIN_USER) {
        $tpl->redirect('adminreports.php');
    } else {
        $tpl->error(ERROR_NO_ACCESS);
    }
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// zu übersetzende Sprache anzeigen
$translang = isset($_REQUEST['translang']) ? strtoupper($_REQUEST['translang']) : strtoupper(
    $opt['template']['locale']
);
if (!isset($opt['locale'][$translang])) {
    $action = 'selectlang';
}

// prüfen, ob die aktuelle data.sql eingespielt wurde
if (calcDataSqlChecksum(true) != getSysConfig('datasql_checksum', '')) {
    $tpl->assign('datasqlfailed', true);
} else {
    $tpl->assign('datasqlfailed', false);
}

switch ($action) {
    case 'selectlang':
        break;

    case 'verify':
        verify();
        break;

    case 'resetids':
        resetIds();
        break;

    case 'clearcache':
        clearCache();
        break;

    case 'export':
        export();
        break;

    case 'xmlexport':
        xmlexport();
        break;

    case 'xmlimport':
        break;

    case 'xmlimport2':
        xmlimport2();
        break;

    case 'xmlimport3':
        xmlimport3();
        break;

    case 'textexportnew':
        textexport($translang, false);
        break;

    case 'textexportall':
        textexport($translang, true);
        break;

    case 'textimport':
        break;

    case 'textimport2':
        textimport($translang);
        break;

    case 'edit':
        if (!$access->mayTranslate($translang)) {
            $tpl->error(ERROR_NO_ACCESS);
        }
        edit();
        break;

    case 'copy_en':
        copy_english_texts();
        break;

    case 'listfaults':
        $trans = sql(
            "SELECT
                `sys_trans`.`id`,
                `sys_trans`.`text`
             FROM `sys_trans`
             LEFT JOIN `sys_trans_ref`
                ON `sys_trans`.`id`=`sys_trans_ref`.`trans_id`
             WHERE ISNULL(`sys_trans_ref`.`trans_id`)
             ORDER BY `sys_trans`.`id` DESC"
        );
        $tpl->assign_rs('trans', $trans);
        sql_free_result($trans);
        break;

    case 'listall':
        $trans = sql(
            "SELECT
                `sys_trans`.`id`,
                `sys_trans`.`text`,
                `sys_trans_text`.`text` AS `trans`
             FROM `sys_trans`
             LEFT JOIN `sys_trans_text`
                ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                AND `sys_trans_text`.`lang`='&1'
             ORDER BY `sys_trans`.`id` DESC",
            $translang
        );
        $tpl->assign_rs('trans', $trans);
        sql_free_result($trans);
        break;

    case 'remove':
        if (!$access->mayTranslate($translang)) {
            $tpl->error(ERROR_NO_ACCESS);
        }
        remove();
        break;

    case 'scan':
        scan();
        break;

    case 'scanstart':
        scanStart();
        break;

    case 'scanfile':
        $filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
        scanFile($filename);
        exit;

    case 'quicknone':
        $cookie->un_set('translate_mode');
        break;

    case 'quicknew':
        $cookie->set('translate_mode', 'new');
        break;

    case 'quickall':
        $cookie->set('translate_mode', 'all');
        break;

    default:
        $action = 'listnew';
        $trans = sql(
            "SELECT DISTINCT
                `sys_trans`.`id`,
                `sys_trans`.`text`
             FROM `sys_trans`
             LEFT JOIN `sys_trans_text`
                ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                AND `sys_trans_text`.`lang`='&1'
             LEFT JOIN `sys_trans_ref`
                ON `sys_trans`.`id`=`sys_trans_ref`.`trans_id`
             WHERE ISNULL(`sys_trans_text`.`trans_id`) OR `sys_trans_text`.`text`=''
             ORDER BY `sys_trans`.`id` DESC",
            $translang
        );
        $tpl->assign_rs('trans', $trans);
        sql_free_result($trans);
}

$languages = [];
foreach ($opt['locale'] as $k => $v) {
    if ($access->mayTranslate($k)) {
        $languages[] = $k;
    }
}
$tpl->assign('languages', $languages);

$tpl->assign('translang', $translang);
$tpl->assign('action', $action);
$tpl->display();

/* $truncatLastInsert = true   for downloaded file
 * $truncatLastInsert = false  to sign self generated file (in function export)
 */
function calcDataSqlChecksum($truncateLastInsert)
{
    global $opt;

    if (!file_exists($opt['rootpath'] . '../sql/static-data/data.sql')) {
        return '';
    }

    $content = file_get_contents($opt['rootpath'] . '../sql/static-data/data.sql');

    // at the end is an INSERT of the current checksum
    // to calculate this checksum, we have to trim the end before that statement and the linefeed before
    // windows linefeeds will be converted to linux linefeeds
    $content = str_replace("\r\n", "\n", $content);

    if ($truncateLastInsert == true) {
        $pos = strrpos($content, "INSERT");
        $content = substr($content, 0, $pos);
    }

    while (substr($content, - 1) == "\n") {
        $content = substr($content, 0, - 1);
    }

    return md5($content);
}

function remove()
{
    global $tpl, $translang;

    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] + 0 : 0;

    sql("DELETE FROM `sys_trans` WHERE `id`='&1'", $id);
    sql("DELETE FROM `sys_trans_text` WHERE `trans_id`='&1'", $id);
    sql("DELETE FROM `sys_trans_ref` WHERE `trans_id`='&1'", $id);

    $tpl->redirect('translate.php?translang=' . $translang);
}

function edit()
{
    global $tpl, $translang;

    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] + 0 : 0;

    if (isset($_REQUEST['usetrans'])) {
        $usetransid = $_REQUEST['usetrans'] + 0;

        $rs = sql(
            "SELECT `lang`, `text`
             FROM `sys_trans_text`
             WHERE `trans_id`='&1'",
            $usetransid
        );
        while ($r = sql_fetch_assoc($rs)) {
            sql(
                "INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`)
                 VALUES ('&1', '&2', '&3')
                 ON DUPLICATE KEY UPDATE `text`='&3'",
                $id,
                $r['lang'],
                $r['text']
            );
        }
        sql_free_result($rs);
        $tpl->redirect('translate.php?translang=' . $translang . '&action=edit&id=' . $id);
    } else {
        if (isset($_REQUEST['submit'])) {
            $transText = isset($_REQUEST['transText']) ? $_REQUEST['transText'] : '';
            sql(
                "INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`)
                 VALUES ('&1', '&2', '&3')
                 ON DUPLICATE KEY UPDATE `text`='&3'",
                $id,
                $translang,
                $transText
            );
        }
    }

    $rs = sql("SELECT `id`, `text` FROM `sys_trans` WHERE `id`='&1'", $id);
    if (!$r = sql_fetch_assoc($rs)) {
        $tpl->error('Trans id not exists');
    }
    sql_fetch_array($rs);

    $tpl->assign('id', $r['id']);
    $tpl->assign('text', $r['text']);

    $rs = sql(
        "SELECT `resource_name`, `line`
         FROM `sys_trans_ref`
         WHERE `trans_id`='&1'
         ORDER BY resource_name, line ASC",
        $id
    );
    $tpl->assign_rs('transRef', $rs);
    sql_free_result($rs);

    // built sql string to search for texts with little difference (levensthein() would be better, but not available in MYSQL)
    $sWhereSql =
        "SOUNDEX('" . sql_escape($r['text']) . "')=SOUNDEX(`sys_trans`.`text`)
         OR SOUNDEX('" . sql_escape($r['text']) . "')=SOUNDEX(`sys_trans_text`.`text`)";

    $trans = sql(
        "SELECT DISTINCT `sys_trans`.`id`, `sys_trans`.`text`
         FROM `sys_trans`
         INNER JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
         WHERE `sys_trans`.`id`!='&1' AND (" . $sWhereSql . ")",
        $id
    );
    $tpl->assign_rs('trans', $trans);
    sql_free_result($trans);

    $tpl->assign(
        'transText',
        sql_value(
            "SELECT `text`
             FROM `sys_trans_text`
             WHERE `trans_id`='&1' AND `lang`='&2'",
            '',
            $id,
            $translang
        )
    );
}

function clearCache()
{
    global $tpl, $translang, $translationHandler;

    unlinkFiles('cache2', 'php');

    unlinkFiles('cache2/smarty/cache', 'tpl');
    unlinkFiles('cache2/smarty/compiled', 'inc');
    unlinkFiles('cache2/smarty/compiled', 'php');
    unlinkFiles('cache2/captcha', 'jpg');
    unlinkFiles('cache2/captcha', 'txt');

    $translationHandler->createMessageFiles();

    $tpl->redirect('translate.php?translang=' . $translang);
}

function unlinkFiles($relbasedir, $ext)
{
    global $opt;

    if (substr($relbasedir, - 1, 1) != '/') {
        $relbasedir .= '/';
    }

    if ($opt['rootpath'] . $relbasedir) {
        if ($dh = opendir($opt['rootpath'] . $relbasedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && is_file($opt['rootpath'] . $relbasedir . $file)) {
                    if (substr($file, - (strlen($ext) + 1), strlen($ext) + 1) == '.' . $ext) {
                        unlink($opt['rootpath'] . $relbasedir . $file);
                    }
                }
            }
        }
        closedir($dh);
    }
}

function resetIds()
{
    global $translang, $tpl;

    if (sql_connect_maintenance() == false) {
        $tpl->error(ERROR_DB_NO_ROOT);
    }

    // clean up dead refs
    sql_temp_table('transDeadIds');
    sql(
        "CREATE TEMPORARY TABLE &transDeadIds (`trans_id` INT(11) PRIMARY KEY)
            SELECT DISTINCT `sys_trans_ref`.`trans_id`
            FROM `sys_trans_ref`
            LEFT JOIN `sys_trans` ON `sys_trans_ref`.`trans_id`=`sys_trans`.`id`
            WHERE ISNULL(`sys_trans`.`id`)"
    );
    sql(
        "DELETE `sys_trans_ref`
         FROM `sys_trans_ref`, &transDeadIds
         WHERE `sys_trans_ref`.`trans_id`=&transDeadIds.`trans_id`"
    );
    sql_drop_temp_table('transDeadIds');

    sql_temp_table('transDeadIds');
    sql(
        "CREATE TEMPORARY TABLE &transDeadIds (`trans_id` INT(11) PRIMARY KEY)
            SELECT DISTINCT `sys_trans_text`.`trans_id`
            FROM `sys_trans_text`
            LEFT JOIN `sys_trans` ON `sys_trans_text`.`trans_id`=`sys_trans`.`id`
            WHERE ISNULL(`sys_trans`.`id`)"
    );
    sql(
        "DELETE `sys_trans_text`
         FROM `sys_trans_text`, &transDeadIds
         WHERE `sys_trans_text`.`trans_id`=&transDeadIds.`trans_id`"
    );
    sql_drop_temp_table('transDeadIds');

    // table sys_trans
    if (sql_value("SELECT COUNT(*) FROM `sys_trans` WHERE `id`=1", 0) == 0) {
        useId(1);
    }

    $lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);

    while ($id = sql_value(
        "SELECT `s1`.`id`+1
         FROM `sys_trans` AS `s1`
         LEFT JOIN `sys_trans` AS `s2` ON `s1`.`id`+1=`s2`.`id`
         WHERE ISNULL(`s2`.`id`) AND `s1`.`id`<'&1'
         ORDER BY `s1`.`id` LIMIT 1",
        0,
        $lastId
    )) {
        if ($lastId + 1 == $id) {
            break;
        }
        setId($lastId, $id);
        $lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
    }

    // need alter privileges
    $lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
    sql("ALTER TABLE `sys_trans` AUTO_INCREMENT = &1", $lastId + 1);

    $tpl->redirect('translate.php?translang=' . $translang);
}

function useId($freeId)
{
    $lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
    if ($lastId + 1 == $freeId) {
        return;
    }
    setId($lastId, $freeId);
}

function setId($oldId, $newId)
{
    global $transIdCols;

    sql("UPDATE `sys_trans` SET `id`='&1' WHERE `id`='&2'", $newId, $oldId);
    sql("UPDATE `sys_trans_ref` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
    sql("UPDATE `sys_trans_text` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);

    foreach ($transIdCols as $col) {
        sql(
            "UPDATE `" . $col['table'] . "`
             SET `" . $col['trans_id'] . "`='&1'
             WHERE `" . $col['trans_id'] . "`='&2'",
            $newId,
            $oldId
        );
    }
}

function export()
{
    global $opt, $tpl, $translang;

    $structure = enumSqlFiles($opt['rootpath'] . '../sql/tables');
    foreach ($structure as $sTable) {
        sql_export_structure_to_file($opt['rootpath'] . '../sql/tables/' . $sTable . '.sql', $sTable);
    }

    // static data tables
    $stab[] = 'attribute_categories';
    $stab[] = 'attribute_groups';
    $stab[] = 'cache_attrib';
    $stab[] = 'cache_logtype';
    $stab[] = 'cache_report_reasons';
    $stab[] = 'cache_report_status';
    $stab[] = 'cache_size';
    $stab[] = 'cache_status';
    $stab[] = 'cache_type';
    $stab[] = 'coordinates_type';
    $stab[] = 'countries';
    $stab[] = 'countries_list_default';
    $stab[] = 'countries_options';
    $stab[] = 'helppages';
    $stab[] = 'languages';
    $stab[] = 'languages_list_default';
    $stab[] = 'logentries_types';
    $stab[] = 'log_types';
    $stab[] = 'news_topics';
    $stab[] = 'nodes';
    $stab[] = 'object_types';
    $stab[] = 'profile_options';
    $stab[] = 'replication_overwritetypes';
    $stab[] = 'search_ignore';
    $stab[] = 'statpics';
    $stab[] = 'sys_menu';
    $stab[] = 'sys_trans';
    $stab[] = 'sys_trans_ref';
    $stab[] = 'sys_trans_text';
    $stab[] = 'towns';
    $stab[] = 'watches_waitingtypes';

    sql_export_tables_to_file($opt['rootpath'] . '../sql/static-data/data.sql', $stab);

    $checksum = calcDataSqlChecksum(false);
    $f = fopen($opt['rootpath'] . '../sql/static-data/data.sql', 'a');
    fwrite(
        $f,
        "INSERT INTO `sysconfig` (`name`, `value`)"
        . " VALUES ('datasql_checksum', '" . sql_escape($checksum) . "')"
        . " ON DUPLICATE KEY UPDATE `value`='" . sql_escape($checksum) . "';\n"
    );
    fclose($f);

    setSysConfig('datasql_checksum', $checksum);

    $tpl->redirect('translate.php?translang=' . $translang);
}

function enumSqlFiles($dir)
{
    $retval = [];
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file, - 4) == '.sql') {
                    $retval[] = substr($file, 0, strlen($file) - 4);
                }
            }
            closedir($dh);
        }
    }

    return $retval;
}

function scan()
{
    global $tpl, $msDirlist;

    $files = [];
    foreach ($msDirlist as $dir) {
        $hDir = opendir($dir);
        if ($hDir !== false) {
            while (($file = readdir($hDir)) !== false) {
                if (is_file($dir . '/' . $file)) {
                    if ((substr($file, - 4) == '.tpl') || (substr($file, - 4) == '.php')) {
                        $files[] = $dir . '/' . $file;
                    }
                }
            }
            closedir($hDir);
        }
    }

    $tpl->assign('files', $files);
}

function scanStart()
{
    global $translationHandler, $transIdCols;

    $translationHandler->clearReferences();

    foreach ($transIdCols as $col) {
        $translationHandler->importFromTable($col['table'], $col['text'], $col['trans_id']);
    }
}

function scanFile($filename)
{
    global $msDirlist, $translationHandler;

    /* check if supplied filename is within allowed path!
     */
    $bFound = false;
    foreach ($msDirlist as $dir) {
        if (substr($dir, - 1) != '/') {
            $dir .= '/';
        }

        if (substr($filename, 0, strlen($dir)) == $dir) {
            $file = substr($filename, strlen($dir));
            if (strpos($file, '/') === false) {
                if ((substr($filename, - 4) == '.tpl') || (substr($filename, - 4) == '.php')) {
                    $bFound = true;
                    break;
                }
            }
        }
    }
    if ($bFound == false) {
        return;
    }

    if (file_exists($filename) == false) {
        return;
    }

    $transFileScan = new translate_filescan($filename);
    $transFileScan->parse();

    foreach ($transFileScan->textlist as $item) {
        $translationHandler->addText($item['text'], $filename, $item['line']);
    }

    exit;
}

function xmlexport()
{
    global $opt;

    header('Content-type:application/octet-stream');
    header('Content-Disposition:attachment;filename="translation.xml"');

    $lang = [];
    foreach ($opt['template']['locales'] as $k => $v) {
        $lang[] = $k;
    }

    @date_default_timezone_set("GMT");
    $writer = new XMLWriter();
    $writer->openURI('php://output');
    $writer->startDocument('1.0', 'UTF-8', 'yes');
    $writer->setIndent(2);

    $writer->startElement('translation');
    $writer->writeAttribute('version', '1.0');
    $writer->writeAttribute('timestamp', date('c'));

    $rs = sql(
        "SELECT `id`, `text`
         FROM `sys_trans`
         ORDER BY `id` ASC"
    );
    while ($r = sql_fetch_assoc($rs)) {
        $writer->startElement('text');
        $writer->writeAttribute('id', $r['id']);

        $writer->writeElement('code', $r['text']);
        for ($n = 0; $n < count($lang); $n ++) {
            $writer->writeElement(
                $lang[$n],
                sql_value(
                    "SELECT `text`
                     FROM `sys_trans_text`
                     WHERE `trans_id`='&1' AND `lang`='&2'",
                    '',
                    $r['id'],
                    $lang[$n]
                )
            );
        }
        $rsUsage = sql(
            "SELECT `resource_name`, `line`
             FROM `sys_trans_ref`
             WHERE `trans_id`='&1'",
            $r['id']
        );
        while ($rUsage = sql_fetch_assoc($rsUsage)) {
            $line = '';
            if ($rUsage['line'] != 0) {
                $line = ' (' . $rUsage['line'] . ')';
            }
            $writer->writeElement('usage', $rUsage['resource_name'] . $line);
        }
        sql_free_result($rsUsage);

        $writer->endElement();
    }
    sql_free_result($rs);

    $writer->endElement();
    $writer->endDocument();
    $writer->flush();
    exit;
}

function xmlimport2()
{
    global $translate, $tpl, $opt;

    if (!isset($_FILES['xmlfile']) || ($_FILES['xmlfile']['error'] != UPLOAD_ERR_OK)) {
        $tpl->error($translate->t('File upload failed!', '', '', 0));
    }

    $scanlang = [];
    foreach ($opt['locale'] as $k => $v) {
        if (isset($_REQUEST['lang' . $k]) && ($_REQUEST['lang' . $k] == '1')) {
            $scanlang[] = $k;
        }
    }

    $doc = new DOMDocument();
    if ($doc->load($_FILES['xmlfile']['tmp_name']) == false) {
        $tpl->error($translate->t('XML file could not be loaded!', '', '', 0));
    }

    /* $saTexts[code_text]['id']
     * $saTexts[code_text]['code']
     * $saTexts[code_text]['de']['old']
     * $saTexts[code_text]['de']['new']
     * $saTexts[code_text]['en']['old']
     * $saTexts[code_text]['en']['new']
     * $saTexts[code_text]['...']
     */
    $saTexts = [];

    foreach ($doc->documentElement->childNodes as $textnode) {
        if ($textnode->nodeType == XML_ELEMENT_NODE) {
            $codeElements = $textnode->getElementsByTagName('code');
            foreach ($scanlang as $lang) {
                $langElements = $textnode->getElementsByTagName($lang);

                $sCodeText = $codeElements->item(0)->nodeValue;
                $sLangText = $langElements->item(0)->nodeValue;

                $transId = sql_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $sCodeText);
                if ($transId == 0) {
                    if ($sLangText != '') {
                        // text not in sys_trans => code changed while translation has been done
                        $saTexts[$sCodeText]['id'] = 0;
                        $saTexts[$sCodeText]['count'] = count($saTexts);
                        $saTexts[$sCodeText]['type'] = 1;
                        $saTexts[$sCodeText]['code'] = $sCodeText;
                        $saTexts[$sCodeText][$lang]['new'] = $sLangText;
                        $saTexts[$sCodeText][$lang]['old'] = '';
                    }
                } else {
                    $sOldText = sql_value(
                        "SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'",
                        '',
                        $transId,
                        $lang
                    );
                    if (($sOldText == '') && ($sLangText != '')) {
                        // new translation
                        $saTexts[$sCodeText]['id'] = $transId;
                        $saTexts[$sCodeText]['count'] = count($saTexts);
                        $saTexts[$sCodeText]['type'] = 2;
                        $saTexts[$sCodeText]['code'] = $sCodeText;
                        $saTexts[$sCodeText][$lang]['new'] = $sLangText;
                        $saTexts[$sCodeText][$lang]['old'] = $sOldText;
                    } else {
                        if ($sOldText != $sLangText) {
                            // translation changed
                            $saTexts[$sCodeText]['id'] = $transId;
                            $saTexts[$sCodeText]['count'] = count($saTexts);
                            $saTexts[$sCodeText]['type'] = 3;
                            $saTexts[$sCodeText]['code'] = $sCodeText;
                            $saTexts[$sCodeText][$lang]['new'] = $sLangText;
                            $saTexts[$sCodeText][$lang]['old'] = $sOldText;
                        }
                    }
                }
            }
        }
    }

    $tpl->assign('texts', $saTexts);
}

function xmlimport3()
{
    global $opt, $translang, $tpl;

    $nCount = isset($_REQUEST['count']) ? $_REQUEST['count'] + 0 : 0;

    for ($nIndex = 1; $nIndex <= $nCount; $nIndex ++) {
        if (isset($_REQUEST['useitem' . $nIndex]) && ($_REQUEST['useitem' . $nIndex] == '1')) {
            $sCode = base64_decode($_REQUEST['code' . $nIndex]);
            $transId = sql_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $sCode);

            if ($transId != 0) {
                foreach ($opt['locale'] as $k => $v) {
                    if (isset($_REQUEST[$k . $nIndex . 'new'])) {
                        $sText = base64_decode($_REQUEST[$k . $nIndex . 'new']);

                        sql(
                            "INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`)
                             VALUES ('&1', '&2', '&3')
                             ON DUPLICATE KEY UPDATE `text`='&3'",
                            $transId,
                            $k,
                            $sText
                        );
                    }
                }
            }
        }
    }

    $tpl->redirect('translate.php?translang=' . $translang);
}

function textexport($translang, $all)
{
    global $opt;

    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="translation.txt"');

    $rs = sql(
        "SELECT
            `id`,
            IFNULL(`sys_trans_text`.`text`, `sys_trans`.`text`) AS `text`
         FROM `sys_trans`
         LEFT JOIN `sys_trans_text`
            ON `sys_trans_text`.`trans_id`=`sys_trans`.`id`
            AND `sys_trans_text`.`lang`='EN'
         ORDER BY `id` ASC"
    );
    while ($r = sql_fetch_assoc($rs)) {
        $translated = sql_value(
            "SELECT `text`
             FROM `sys_trans_text`
             WHERE `trans_id`='&1' AND `lang`='&2'",
            '',
            $r['id'],
            $translang
        );
        if (($all) || (mb_strlen($translated) == 0)) {
            $thisline = $r['id'] . "\r\n";
            $thisline .= $r['text'] . "\r\n";
            $thisline .= $translated . "\r\n";
            $thisline .= "\r\n";
            echo($thisline);
        }
    }
    sql_free_result($rs);

    exit;
}

function textimport($lang)
{
    global $translate, $tpl, $opt;

    if (!isset($_FILES['textfile']) || ($_FILES['textfile']['error'] != UPLOAD_ERR_OK)) {
        $tpl->error($translate->t('File upload failed!', '', '', 0));
    }

    $data = file_get_contents($_FILES['textfile']['tmp_name']);
    $lines = explode("\n", $data);

    $saTexts = [];

    for ($i = 0; $i + 1 < count($lines); $i += 4) {
        $nId = trim($lines[$i]);
        $sEnText = trim($lines[$i + 1]);
        $sLangText = trim($lines[$i + 2]);

        if ($nId != '') {
            $transId = sql_value(
                "SELECT `trans_id`
                 FROM `sys_trans_text`
                 WHERE `trans_id`='&1' AND `lang`='EN' AND BINARY `text`='&2'",
                0,
                $nId,
                $sEnText
            );
            if ($transId == 0) {
                $transId = sql_value(
                    "SELECT `id`
                     FROM `sys_trans`
                     WHERE `id`='&1' AND BINARY `text`='&2'",
                    0,
                    $nId,
                    $sEnText
                );
            }
            if ($transId == 0) {
                if ($sLangText != '') {
                    // text not in sys_trans => code changed while translation has been done
                    $saTexts[$sEnText]['id'] = $nId;
                    $saTexts[$sEnText]['count'] = count($saTexts);
                    $saTexts[$sEnText]['type'] = 1;
                    $saTexts[$sEnText]['code'] = '';
                    $saTexts[$sEnText]['en'] = $sEnText;
                    $saTexts[$sEnText][$lang]['new'] = $sLangText;
                    $saTexts[$sEnText][$lang]['old'] = '';
                }
            } else {
                $sOldText = sql_value(
                    "SELECT `text`
                     FROM `sys_trans_text`
                     WHERE `trans_id`='&1' AND `lang`='&2'",
                    '',
                    $transId,
                    $lang
                );
                $sCodeText = sql_value(
                    "SELECT `text`
                     FROM `sys_trans`
                     WHERE `id`='&1'",
                    '',
                    $transId
                );
                if (($sOldText == '') && ($sLangText != '')) {
                    // new translation
                    $saTexts[$sCodeText]['id'] = $transId;
                    $saTexts[$sCodeText]['count'] = count($saTexts);
                    $saTexts[$sCodeText]['type'] = 2;
                    $saTexts[$sCodeText]['code'] = $sCodeText;
                    $saTexts[$sCodeText]['en'] = $sEnText;
                    $saTexts[$sCodeText][$lang]['new'] = $sLangText;
                    $saTexts[$sCodeText][$lang]['old'] = $sOldText;
                } else {
                    if ($sOldText != $sLangText) {
                        // translation changed
                        $saTexts[$sCodeText]['id'] = $transId;
                        $saTexts[$sCodeText]['count'] = count($saTexts);
                        $saTexts[$sCodeText]['type'] = 3;
                        $saTexts[$sCodeText]['code'] = $sCodeText;
                        $saTexts[$sCodeText]['en'] = $sEnText;
                        $saTexts[$sCodeText][$lang]['new'] = $sLangText;
                        $saTexts[$sCodeText][$lang]['old'] = $sOldText;
                    }
                }
            }
        }
    }

    $tpl->assign('texts', $saTexts);
}

function copy_english_texts()
{
    sql_temp_table('transtmp');
    sql(
        "CREATE TEMPORARY TABLE &transtmp
            SELECT `st`.`id` AS `trans_id`, 'EN' AS `lang`, `st`.`text`
            FROM `sys_trans` `st`
            LEFT JOIN `sys_trans_text` `stt`
                ON `stt`.`trans_id`=`st`.`id`
                AND `stt`.`lang`='EN'
            WHERE `stt`.`trans_id` IS NULL"
    );
    sql(
        "INSERT INTO `sys_trans_text`
         SELECT *, NULL FROM &transtmp"
    );
    sql_drop_temp_table('transtmp');
}

function addClassesDirectoriesToDirlist($basedir)
{
    global $msDirlist;
    $msDirlist[] = $basedir;

    $hDir = opendir($basedir);
    if (!$hDir) {
        return;
    }

    if (substr($basedir, 0, - 1) != '/') {
        $basedir .= '/';
    }

    while (($file = readdir($hDir)) !== false) {
        if ($file != '.' && $file != '..' && is_dir($basedir . $file)) {
            addClassesDirectoriesToDirlist($basedir . $file);
        }
    }
    closedir($hDir);
}

function verify()
{
    global $tpl, $transIdCols;

    $inconsistencies = [];
    foreach ($transIdCols as $col) {
        if (!isset($col['verify']) || $col['verify']) {
            $rs = sql(
                "SELECT
                    `" . $col['text'] . "` `text`,
                    `" . $col['trans_id'] . "` `trans_id`
                 FROM `" . $col['table'] . "`"
            );
            while ($r = sql_fetch_assoc($rs)) {
                $st = sql_value(
                    "SELECT `text`
                     FROM `sys_trans`
                     WHERE `id`='&1'",
                    false,
                    $r['trans_id']
                );
                $en = sql_value(
                    "SELECT `text`
                     FROM `sys_trans_text`
                     WHERE `trans_id`='&1' AND `lang`='EN'",
                    false,
                    $r['trans_id']
                );
                if ($en != $r['text'] || $st != $r['text']) {
                    $inconsistencies[] = [
                        'id' => $r['trans_id'],
                        'col' => $col['table'] . '.' . $col['text'],
                        'org_text' => $r['text'],
                        'sys_trans' => $st,
                        'en_text' => $en,
                    ];
                }
            }
        }
    }
    $tpl->assign('inconsistencies', $inconsistencies);
}
