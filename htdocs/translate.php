<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	/* config section
	 */
	global $msDirlist;
	$msDirlist = array();
	$msDirlist[] = '.';
	$msDirlist[] = './config2';
	$msDirlist[] = './lang/de';
	$msDirlist[] = './lang/de/ocstyle';
	$msDirlist[] = './lang/de/ocstyle/lib';
	$msDirlist[] = './lib';
	$msDirlist[] = './lib2';
	$msDirlist[] = './lib2/logic';
	$msDirlist[] = './templates2/mail';
	$msDirlist[] = './templates2/ocstyle';
	
	// directory libse needs to be added recursive
	addClassesDirecotriesToDirlist('libse');

	require('./lib2/web.inc.php');
	require_once('./lib2/translate.class.php');
	require_once('./lib2/translationHandler.class.php');
	require_once('./lib2/translate_filescan.class.php');
	require_once('./lib2/translateAccess.php');

	$tpl->name = 'translate';
	$tpl->menuitem = MNU_ADMIN_TRANSLATE;

	$login->verify();
	$access = new translateAccess();

	if (!$access->hasAccess())
		$tpl->error(ERROR_NO_ACCESS);

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	// zu übersetzende Sprache anzeigen
	$translang = isset($_REQUEST['translang']) ? strtoupper($_REQUEST['translang']) : strtoupper($opt['template']['locale']);
	if (!isset($opt['locale'][$translang]))
		$action = 'selectlang';

	// prüfen, ob die aktuelle data.sql eingespielt wurde
	if (calcDataSqlChecksum(true) != getSysConfig('datasql_checksum', ''))
		$tpl->assign('datasqlfailed', true);
	else
		$tpl->assign('datasqlfailed', false);

	if ($action == 'selectlang')
	{
	}
	else if ($action == 'resetids')
		resetIds();
	else if ($action == 'clearcache')
		clearCache();
	else if ($action == 'export')
		export();
	else if ($action == 'xmlexport')
		xmlexport();
	else if ($action == 'xmlimport')
	{
	}
	else if ($action == 'xmlimport2')
		xmlimport2();
	else if ($action == 'xmlimport3')
		xmlimport3();
	else if ($action == 'textexportnew')
		textexport($translang, false);
	else if ($action == 'textexportall')
		textexport($translang, true);
	else if ($action == 'textimport')
	{
	}
	else if ($action == 'textimport2')
		textimport($translang);
	else if ($action == 'edit')
	{
		if (!$access->mayTranslate($translang))
			$tpl->error(ERROR_NO_ACCESS);

		edit();
	}
	else if ($action == 'copy_en')
		copy_english_texts();
	else if ($action == 'listfaults')
	{
		$trans = sql("SELECT `sys_trans`.`id`, `sys_trans`.`text` FROM `sys_trans` LEFT JOIN `sys_trans_ref` ON `sys_trans`.`id`=`sys_trans_ref`.`trans_id` WHERE ISNULL(`sys_trans_ref`.`trans_id`) ORDER BY `sys_trans`.`id` DESC");
		$tpl->assign_rs('trans', $trans);
		sql_free_result($trans);
	}
	else if ($action == 'listall')
	{
		$trans = sql("SELECT `sys_trans`.`id`, `sys_trans`.`text`, `sys_trans_text`.`text` AS `trans` FROM `sys_trans` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `sys_trans`.`id` DESC", $translang);
		$tpl->assign_rs('trans', $trans);
		sql_free_result($trans);
	}
	else if ($action == 'remove')
	{
		if (!$access->mayTranslate($translang))
			$tpl->error(ERROR_NO_ACCESS);

		remove();
	}
	else if ($action == 'scan')
	{
		scan();
	}
	else if ($action == 'scanstart')
	{
		scanStart();
		exit;
	}
	else if ($action == 'scanfile')
	{
		$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
		scanFile($filename);
		exit;
	}
	else
	{
		if ($action == 'quicknone')
			$cookie->un_set('translate_mode');
		else if ($action == 'quicknew')
			$cookie->set('translate_mode', 'new');
		else if ($action == 'quickall')
			$cookie->set('translate_mode', 'all');

		$action = 'listnew';

		$trans = sql("SELECT DISTINCT `sys_trans`.`id`, `sys_trans`.`text` FROM `sys_trans` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' LEFT JOIN `sys_trans_ref` ON `sys_trans`.`id`=`sys_trans_ref`.`trans_id` WHERE (ISNULL(`sys_trans_text`.`trans_id`) OR `sys_trans_text`.`text`='') ORDER BY `sys_trans`.`id` DESC", $translang);
		$tpl->assign_rs('trans', $trans);
		sql_free_result($trans);
	}

	$languages = array();
	foreach ($opt['locale'] AS $k => $v)
	{
		if ($access->mayTranslate($k))
			$languages[] = $k;
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

	if (!file_exists($opt['rootpath'] . 'doc/sql/static-data/data.sql'))
		return '';

	$content = file_get_contents($opt['rootpath'] . 'doc/sql/static-data/data.sql');

	// at the end is an INSERT of the current checksum
	// to calculate this checksum, we have to trim the end before that statement and the linefeed before
	// windows linefeeds will be converted to linux linefeeds
	$content = str_replace("\r\n", "\n", $content);

	if ($truncateLastInsert == true)
	{
		$pos = strrpos($content, "INSERT");
		$content = substr($content, 0, $pos);
	}

	while (substr($content, -1) == "\n")
		$content = substr($content, 0, -1);

	return md5($content);
}

function remove()
{
	global $tpl, $translang;

	$id = isset($_REQUEST['id']) ? $_REQUEST['id']+0 : 0;

	sql("DELETE FROM `sys_trans` WHERE `id`='&1'", $id);
	sql("DELETE FROM `sys_trans_text` WHERE `trans_id`='&1'", $id);
	sql("DELETE FROM `sys_trans_ref` WHERE `trans_id`='&1'", $id);

	$tpl->redirect('translate.php?translang=' . $translang);
}

function edit()
{
	global $tpl, $translang;

	$id = isset($_REQUEST['id']) ? $_REQUEST['id']+0 : 0;

	if (isset($_REQUEST['usetrans']))
	{
		$usetransid = $_REQUEST['usetrans']+0;

		$rs = sql("SELECT `lang`, `text` FROM `sys_trans_text` WHERE `trans_id`='&1'", $usetransid);
		while ($r = sql_fetch_assoc($rs))
		{
			sql("INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`) VALUES ('&1', '&2', '&3') ON DUPLICATE KEY UPDATE `text`='&3'", $id, $r['lang'], $r['text']);
		}
		sql_free_result($rs);
		$tpl->redirect('translate.php?translang=' . $translang . '&action=edit&id=' . $id);
	}
	else if (isset($_REQUEST['submit']))
	{
		$transText = isset($_REQUEST['transText']) ? $_REQUEST['transText'] : '';
		sql("INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`) VALUES ('&1', '&2', '&3') ON DUPLICATE KEY UPDATE `text`='&3'", $id, $translang, $transText);
	}

	$rs = sql("SELECT `id`, `text` FROM `sys_trans` WHERE `id`='&1'", $id);
	if (!$r = sql_fetch_assoc($rs))
		$tpl->error('Trans id not exists');
	sql_fetch_array($rs);

	$tpl->assign('id', $r['id']);
	$tpl->assign('text', $r['text']);

	$rs = sql("SELECT `resource_name`, `line` FROM `sys_trans_ref` WHERE `trans_id`='&1' ORDER BY resource_name, line ASC", $id);
	$tpl->assign_rs('transRef', $rs);
	sql_free_result($rs);

	// built sql string to search for texts with little difference (levensthein() would be better, but not available in MYSQL)
	$sWhereSql = "SOUNDEX('" . sql_escape($r['text']) . "')=SOUNDEX(`sys_trans`.`text`) OR SOUNDEX('" . sql_escape($r['text']) . "')=SOUNDEX(`sys_trans_text`.`text`)";

	$trans = sql("SELECT DISTINCT `sys_trans`.`id`, `sys_trans`.`text` FROM `sys_trans` INNER JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` WHERE `sys_trans`.`id`!='&1' AND (" . $sWhereSql . ")", $id);
	$tpl->assign_rs('trans', $trans);
	sql_free_result($trans);

	$tpl->assign('transText', sql_value("SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'", '', $id, $translang));
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

	if (substr($relbasedir, -1, 1) != '/')
		$relbasedir .= '/';

	if ($opt['rootpath'] . $relbasedir)
	{
		if ($dh = opendir($opt['rootpath'] . $relbasedir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if ($file != '.' && $file != '..' && is_file($opt['rootpath'] . $relbasedir . $file))
				{
					if (substr($file, -(strlen($ext)+1), strlen($ext)+1) == '.' . $ext)
					{
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

	if (sql_connect_maintenance() == false)
		$tpl->error(ERROR_DB_NO_ROOT);

	// clean up dead refs
	sql_temp_table('transDeadIds');
	sql("CREATE TEMPORARY TABLE &transDeadIds (`trans_id` INT(11) PRIMARY KEY) SELECT `sys_trans_ref`.`trans_id` FROM `sys_trans_ref` LEFT JOIN `sys_trans` ON `sys_trans_ref`.`trans_id`=`sys_trans`.`id` WHERE ISNULL(`sys_trans`.`id`)");
	sql("DELETE `sys_trans_ref` FROM `sys_trans_ref`, &transDeadIds WHERE `sys_trans_ref`.`trans_id`=&transDeadIds.`trans_id`");
	sql_drop_temp_table('transDeadIds');

	sql_temp_table('transDeadIds');
	sql("CREATE TEMPORARY TABLE &transDeadIds (`trans_id` INT(11) PRIMARY KEY) SELECT `sys_trans_text`.`trans_id` FROM `sys_trans_text` LEFT JOIN `sys_trans` ON `sys_trans_text`.`trans_id`=`sys_trans`.`id` WHERE ISNULL(`sys_trans`.`id`)");
	sql("DELETE `sys_trans_text` FROM `sys_trans_text`, &transDeadIds WHERE `sys_trans_text`.`trans_id`=&transDeadIds.`trans_id`");
	sql_drop_temp_table('transDeadIds');

	// table sys_trans
	if (sql_value("SELECT COUNT(*) FROM `sys_trans` WHERE `id`=1", 0) == 0)
		useId(1);

	$lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);

	while ($id = sql_value("SELECT `s1`.`id`+1 FROM `sys_trans` AS `s1` LEFT JOIN `sys_trans` AS `s2` ON `s1`.`id`+1=`s2`.`id` WHERE ISNULL(`s2`.`id`) AND `s1`.`id`<'&1' ORDER BY `s1`.`id` LIMIT 1", 0, $lastId))
	{
		if ($lastId+1 == $id) break;
		setId($lastId, $id);
		$lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
	}

	// need alter privileges
	$lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
	sql("ALTER TABLE `sys_trans` AUTO_INCREMENT = &1", $lastId+1);

	$tpl->redirect('translate.php?translang=' . $translang);
}

function useId($freeId)
{
	$lastId = sql_value("SELECT MAX(`id`) FROM `sys_trans`", 0);
	if ($lastId+1 == $freeId) return;
	setId($lastId, $freeId);
}

function setId($oldId, $newId)
{
	sql("UPDATE `sys_trans` SET `id`='&1' WHERE `id`='&2'", $newId, $oldId);
	sql("UPDATE `sys_trans_ref` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `sys_trans_text` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `countries` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `languages` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `cache_size` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `cache_status` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `cache_type` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `log_types` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `sys_menu` SET `title_trans_id`='&1' WHERE `title_trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `sys_menu` SET `menustring_trans_id`='&1' WHERE `menustring_trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `profile_options` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `attribute_categories` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `attribute_groups` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `cache_attrib` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `cache_attrib` SET `html_desc_trans_id`='&1' WHERE `html_desc_trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `statpics` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
	sql("UPDATE `coordinates_type` SET `trans_id`='&1' WHERE `trans_id`='&2'", $newId, $oldId);
}

function export()
{
	global $opt, $tpl, $translang;

	$structure = enumSqlFiles($opt['rootpath'] . "doc/sql/tables");
	foreach ($structure AS $sTable)
		sql_export_structure_to_file($opt['rootpath'] . 'doc/sql/tables/' . $sTable . '.sql', $sTable);

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
	$stab[] = 'watches_waitingtypes';

	sql_export_tables_to_file($opt['rootpath'] . 'doc/sql/static-data/data.sql', $stab);

	$checksum = calcDataSqlChecksum(false);
	$f = fopen($opt['rootpath'] . 'doc/sql/static-data/data.sql', 'a');
	fwrite($f, "INSERT INTO `sysconfig` (`name`, `value`) VALUES ('datasql_checksum', '" . sql_escape($checksum) . "') ON DUPLICATE KEY UPDATE `value`='" . sql_escape($checksum) . "';");
	fclose($f);

	setSysConfig('datasql_checksum', $checksum);

	$tpl->redirect('translate.php?translang=' . $translang);
}

function enumSqlFiles($dir)
{
	$retval = array();
	if (is_dir($dir))
	{
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (substr($file, -4) == '.sql')
					$retval[] = substr($file, 0, strlen($file) - 4);
			}
			closedir($dh);
		}
	}
	return $retval;
}

function scan()
{
	global $tpl, $msDirlist;

	$files = array();
	foreach ($msDirlist AS $dir)
	{
		$hDir = opendir($dir);
		if ($hDir !== false)
		{
			while (($file = readdir($hDir)) !== false)
			{
				if (is_file($dir . '/' . $file))
				{
					if ((substr($file, -4) == '.tpl') || (substr($file, -4) == '.php'))
					{
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
	global $translationHandler;

	$translationHandler->clearReferences();

	$translationHandler->importFromTable('countries', 'name', 'trans_id');
	$translationHandler->importFromTable('languages', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_size', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_status', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_type', 'name', 'trans_id');
	$translationHandler->importFromTable('coordinates_type', 'name', 'trans_id');
	$translationHandler->importFromTable('log_types', 'name', 'trans_id');
	$translationHandler->importFromTable('sys_menu', 'title', 'title_trans_id');
	$translationHandler->importFromTable('sys_menu', 'menustring', 'menustring_trans_id');
	$translationHandler->importFromTable('cache_report_status', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_report_reasons', 'name', 'trans_id');
	$translationHandler->importFromTable('profile_options', 'name', 'trans_id');
	$translationHandler->importFromTable('attribute_groups', 'name', 'trans_id');
	$translationHandler->importFromTable('attribute_categories', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_attrib', 'name', 'trans_id');
	$translationHandler->importFromTable('cache_attrib', 'html_desc', 'html_desc_trans_id');
	$translationHandler->importFromTable('statpics', 'description', 'trans_id');
}

function scanFile($filename)
{
	global $msDirlist, $translationHandler;

	/* check if supplied filename is within allowed path!
	 */
	$bFound = false;
	foreach ($msDirlist AS $dir)
	{
		if (substr($dir, -1) != '/')
			$dir .= '/';

		if (substr($filename, 0, strlen($dir)) == $dir)
		{
			$file = substr($filename, strlen($dir));
			if (strpos($file, '/') === false)
			{
				if ((substr($filename, -4) == '.tpl') || (substr($filename, -4) == '.php'))
				{
					$bFound = true;
					break;
				}
			}
		}
	}
	if ($bFound == false)
		return;

	if (file_exists($filename) == false)
		return;

	$transFileScan = new translate_filescan($filename);
	$transFileScan->parse();

	foreach ($transFileScan->textlist AS $item)
	{
		$translationHandler->addText($item['text'], $filename, $item['line']);
	}

	exit;
}

function xmlexport()
{
	global $opt;

	header('Content-type:application/octet-stream');
	header('Content-Disposition:attachment;filename="translation.xml"');

	$lang = array();
	foreach ($opt['template']['locales'] AS $k => $v)
		$lang[] = $k;

	@date_default_timezone_set("GMT");
	$writer = new XMLWriter();
	$writer->openURI('php://output');
	$writer->startDocument('1.0', 'UTF-8', 'yes');
	$writer->setIndent(2);
	
	$writer->startElement('translation');
		$writer->writeAttribute('version', '1.0');
		$writer->writeAttribute('timestamp', date('c'));

	$rs = sql("SELECT `id`, `text` FROM `sys_trans` ORDER BY `id` ASC");
	while ($r = sql_fetch_assoc($rs))
	{
		$writer->startElement('text');
			$writer->writeAttribute('id', $r['id']);

			$writer->writeElement('code', $r['text']);
			for ($n = 0; $n < count($lang); $n++)
				$writer->writeElement($lang[$n], sql_value("SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'", '', $r['id'], $lang[$n]));
			$rsUsage = sql("SELECT `resource_name`, `line` FROM `sys_trans_ref` WHERE `trans_id`='&1'", $r['id']);
			while ($rUsage = sql_fetch_assoc($rsUsage))
			{
				$line = '';
				if ($rUsage['line'] !=0) $line = ' (' . $rUsage['line'] . ')';
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

	if (!isset($_FILES['xmlfile']) || ($_FILES['xmlfile']['error'] != UPLOAD_ERR_OK))
		$tpl->error($translate->t('File upload failed!', '', '', 0));

	$scanlang = array();
	foreach ($opt['locale'] AS $k => $v)
		if (isset($_REQUEST['lang' . $k]) && ($_REQUEST['lang' . $k]=='1'))
			$scanlang[] = $k;

	$doc = new DOMDocument();
	if ($doc->load($_FILES['xmlfile']['tmp_name']) == false)
		$tpl->error($translate->t('XML file could not be loaded!', '', '', 0));

	/* $saTexts[code_text]['id']
	 * $saTexts[code_text]['code']
	 * $saTexts[code_text]['de']['old']
	 * $saTexts[code_text]['de']['new']
	 * $saTexts[code_text]['en']['old']
	 * $saTexts[code_text]['en']['new']
	 * $saTexts[code_text]['...']
	 */
	$saTexts = array();
	
	foreach ($doc->documentElement->childNodes AS $textnode)
	{
		if ($textnode->nodeType == XML_ELEMENT_NODE)
		{
			$codeElements = $textnode->getElementsByTagName('code');
			foreach ($scanlang AS $lang)
			{
				$langElements = $textnode->getElementsByTagName($lang);
				
				$sCodeText = $codeElements->item(0)->nodeValue;
				$sLangText = $langElements->item(0)->nodeValue;

				$transId = sql_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $sCodeText);
				if ($transId == 0)
				{
					if ($sLangText != '')
					{
						// text not in sys_trans => code changed while translation has been done
						$saTexts[$sCodeText]['id'] = 0;
						$saTexts[$sCodeText]['count'] = count($saTexts);
						$saTexts[$sCodeText]['type'] = 1;
						$saTexts[$sCodeText]['code'] = $sCodeText;
						$saTexts[$sCodeText][$lang]['new'] = $sLangText;
						$saTexts[$sCodeText][$lang]['old'] = '';
					}
				}
				else
				{
					$sOldText = sql_value("SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'", '', $transId, $lang);
					if (($sOldText == '') && ($sLangText != ''))
					{
						// new translation
						$saTexts[$sCodeText]['id'] = $transId;
						$saTexts[$sCodeText]['count'] = count($saTexts);
						$saTexts[$sCodeText]['type'] = 2;
						$saTexts[$sCodeText]['code'] = $sCodeText;
						$saTexts[$sCodeText][$lang]['new'] = $sLangText;
						$saTexts[$sCodeText][$lang]['old'] = $sOldText;
					}
					else if ($sOldText != $sLangText)
					{
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

	$tpl->assign('texts', $saTexts);
}

function xmlimport3()
{
	global $opt, $translang, $tpl;

	$nCount = isset($_REQUEST['count']) ? $_REQUEST['count']+0 : 0;

	for ($nIndex = 1; $nIndex <= $nCount; $nIndex++)
	{
		if (isset($_REQUEST['useitem' . $nIndex]) && ($_REQUEST['useitem' . $nIndex] == '1'))
		{
			$sCode = base64_decode($_REQUEST['code' . $nIndex]);
			$transId = sql_value("SELECT `id` FROM `sys_trans` WHERE `text`='&1'", 0, $sCode);

			if ($transId != 0)
			{
				foreach ($opt['locale'] AS $k => $v)
				{
					if (isset($_REQUEST[$k . $nIndex . 'new']))
					{
						$sText = base64_decode($_REQUEST[$k . $nIndex . 'new']);

						sql("INSERT INTO `sys_trans_text` (`trans_id`, `lang`, `text`) VALUES ('&1', '&2', '&3') ON DUPLICATE KEY UPDATE `text`='&3'", $transId, $k, $sText);
					}
				}
			}
		}
	}

	$tpl->redirect('translate.php?translang=' . $translang);
}

// 2012-08-24 following - changed output format from tab-separated lines to multiple lines
//                        for better readability, and delimiter from *nix \n to canonical \r\n
function textexport($translang, $all)
{
	global $opt;

	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="translation.txt"');

	$rs = sql("SELECT `id`, `text` FROM `sys_trans` ORDER BY `id` ASC");
	while ($r = sql_fetch_assoc($rs))
	{
		$translated = sql_value("SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'", '', $r['id'], $translang);
		if (($all) || (mb_strlen($translated)==0))
		{
			$thisline = $r['text'];
			$thisline .= "\r\n";
			$thisline .= $translated;
			$thisline .= "\r\n";
			$thisline .= "\r\n";
			echo($thisline);
		}
	}
	sql_free_result($rs);

	exit;
}

// 2012-08-24 following - changed input format from tab-separated lines to multiple lines
function textimport($lang)
{
	global $translate, $tpl, $opt;

	if (!isset($_FILES['textfile']) || ($_FILES['textfile']['error'] != UPLOAD_ERR_OK))
		$tpl->error($translate->t('File upload failed!', '', '', 0));

	$data = file_get_contents($_FILES['textfile']['tmp_name']);
	$lines = explode("\n", $data);

	/* $saTexts[code_text]['id']
	* $saTexts[code_text]['code']
	* $saTexts[code_text]['de']['old']
	* $saTexts[code_text]['de']['new']
	* $saTexts[code_text]['en']['old']
	* $saTexts[code_text]['en']['new']
	* $saTexts[code_text]['...']
	*/
	$saTexts = array();

	for ($i=0; $i+1 < count($lines); $i += 3)
	{
		$sCodeText = trim($lines[$i]);
		$sLangText = trim($lines[$i+1]);

		if ($sCodeText . $sLangText != '')
		{
			$transId = sql_value("SELECT `id` FROM `sys_trans` WHERE BINARY `text`='&1'", 0, $sCodeText);
			if ($transId == 0)
			{
				if ($sLangText != '')
				{
					// text not in sys_trans => code changed while translation has been done
					$saTexts[$sCodeText]['id'] = 0;
					$saTexts[$sCodeText]['count'] = count($saTexts);
					$saTexts[$sCodeText]['type'] = 1;
					$saTexts[$sCodeText]['code'] = $sCodeText;
					$saTexts[$sCodeText][$lang]['new'] = $sLangText;
					$saTexts[$sCodeText][$lang]['old'] = '';
				}
			}
			else
			{
				$sOldText = sql_value("SELECT `text` FROM `sys_trans_text` WHERE `trans_id`='&1' AND `lang`='&2'", '', $transId, $lang);
				if (($sOldText == '') && ($sLangText != ''))
				{
					// new translation
					$saTexts[$sCodeText]['id'] = $transId;
					$saTexts[$sCodeText]['count'] = count($saTexts);
					$saTexts[$sCodeText]['type'] = 2;
					$saTexts[$sCodeText]['code'] = $sCodeText;
					$saTexts[$sCodeText][$lang]['new'] = $sLangText;
					$saTexts[$sCodeText][$lang]['old'] = $sOldText;
				}
				else if ($sOldText != $sLangText)
				{
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

	$tpl->assign('texts', $saTexts);
}

function copy_english_texts()
{
	sql_temp_table('transtmp');
	sql("
			CREATE TEMPORARY TABLE &transtmp
			SELECT `st`.`id` AS `trans_id`, 'EN' AS `lang`, `st`.`text`
			FROM `sys_trans` `st`
	    LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`st`.`id` AND `stt`.`lang`='EN'
	    WHERE `stt`.`trans_id` IS NULL");
	sql("
			INSERT INTO `sys_trans_text`
			SELECT *,NULL FROM &transtmp");
	sql_drop_temp_table('transtmp');
}

function addClassesDirecotriesToDirlist($basedir)
{
	global $msDirlist;
	$msDirlist[] = $basedir;

	$hDir = opendir($basedir);
	if (!$hDir) return;

	if (substr($basedir, 0, -1) != '/')
		$basedir .= '/';

	while (($file = readdir($hDir)) !== false)
	{
		if ($file != '.' && $file != '..' && is_dir($basedir . $file))
		{
			addClassesDirecotriesToDirlist($basedir . $file);
		}
	}
  closedir($hDir);
}

?>