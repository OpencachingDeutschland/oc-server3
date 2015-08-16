<?php
/***************************************************************************
																./editdesc.php
															-------------------
		begin                : July 7 2004

		For license information see doc/license.txt
*****************************************************************************/

/****************************************************************************
	    
   Unicode Reminder メモ

	 edit a cache listing
	
	 used template(s): editcache
	
	 GET/POST Parameter: cacheid, desclang
	
 ****************************************************************************/
 
	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib2/OcHTMLPurifier.class.php');
	
	//Preprocessing
	if ($error == false)
	{
		require($stylepath . '/editdesc.inc.php');

		// check for old-style parameters
		if (isset($_REQUEST['cacheid']) && isset($_REQUEST['desclang']) && !isset($_REQUEST['descid']))  // Ocprop
		{
			$cache_id = $_REQUEST['cacheid'];  // Ocprop
			$desc_lang = $_REQUEST['desclang'];  // Ocprop

			$rs = sql("SELECT `id` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $desc_lang);
			if (mysql_num_rows($rs) == 1)
			{
				$r = sql_fetch_array($rs);
				$descid = $r['id'];
			}
			else
			{
				tpl_errorMsg('editdesc', $error_desc_not_found);
			}
			sql_free_result($rs);
		}
		else
		{
			$descid = isset($_REQUEST['descid']) ? $_REQUEST['descid'] : 0;
			if (is_numeric($descid) == false)
				$descid = 0;
		}

		if ($usr === false)
		{
			$tplname = 'login';
			
			tpl_set_var('username', '');
			tpl_set_var('target', htmlspecialchars('editdesc.php?descid=' . urlencode($descid), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('message_start', "");
			tpl_set_var('message_end', "");
			tpl_set_var('message', $login_required);
			tpl_set_var('helplink', helppagelink('login'));
		}
		else
		{

			$desc_rs = sql("SELECT `cache_desc`.`cache_id` `cache_id`, `cache_desc`.`node` `node`, `cache_desc`.`language` `language`, `caches`.`name` `name`, `caches`.`user_id` `user_id`, `caches`.`wp_oc`, `cache_desc`.`desc` `desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`desc_htmledit` `desc_htmledit` FROM `caches`, `cache_desc` WHERE (`caches`.`cache_id` = `cache_desc`.`cache_id`) AND `cache_desc`.`id`='&1'", $descid);
			$desc_record = sql_fetch_array($desc_rs);
			sql_free_result($desc_rs);

			if ($desc_record !== false)
			{
				$desc_lang = $desc_record['language'];
				$cache_id = $desc_record['cache_id'];

				if ($desc_record['node'] != $oc_nodeid)
				{
					tpl_errorMsg('editdesc', $error_wrong_node);
					exit;
				}

				if ($desc_record['user_id'] == $usr['userid'] || $login->listingAdmin())
				{
					$tplname = 'editdesc';

					tpl_set_var('desc_err', '');
					$show_all_langs = false;

					//save to DB?
					if (isset($_POST['post']))  // Ocprop
					{
						//here we read all used information from the form if submitted
						$descMode = isset($_POST['descMode']) ? $_POST['descMode'] : 1;  // Ocprop

						// fuer alte Versionen von OCProp
						if (isset($_POST['submit']) && !isset($_POST['version2']))
						{
							$descMode = (isset($_POST['desc_html']) && ($_POST['desc_html']==1)) ? 2 : 1;
							$_POST['submitform'] = $_POST['submit'];
						}

						switch ($descMode)
						{
							case 1:
								// plain text mode -- no longer supported, see issue #236;
								// descMode 1 is only retained for Ocprop compatibility
								$desc_html = 0;
								$desc_htmledit = 0;
							case 2:
								// edit HTML as plain text
								$desc_html = 1;
								$desc_htmledit = 0;
								break;
							default:
								// edit HTML in TinyMCE
								$desc_html = 1;
								$desc_htmledit = 1;
								break;
						}
						$short_desc = $_POST['short_desc'];  // Ocprop
						$hint = htmlspecialchars($_POST['hints'], ENT_COMPAT, 'UTF-8');
						$desclang = $_POST['desclang'];
						$show_all_langs = isset($_POST['show_all_langs_value']) ? $_POST['show_all_langs_value'] : 0;
						if (!is_numeric($show_all_langs)) $show_all_langs = 0;

						// fuer alte Versionen von OCProp
						if (isset($_POST['submit']) && !isset($_POST['version2']))
						{
							$short_desc = iconv("ISO-8859-1", "UTF-8", $short_desc);
							$hint = iconv("ISO-8859-1", "UTF-8", $hint);
						}

						if ($desc_html == 1)
						{
							// Text from textarea
							$desc = $_POST['desc'];  // Ocprop
						
							// fuer alte Versionen von OCProp
							if (isset($_POST['submit']) && !isset($_POST['version2']))
							{
								$desc = iconv("ISO-8859-1", "UTF-8", $desc);
							}

							// Filter Input
							$purifier = new OcHTMLPurifier($opt);
							$desc = $purifier->purify($desc);
						}
						else
						{
							// escape text
							$desc = htmlspecialchars($_POST['desc'], ENT_COMPAT, 'UTF-8');

							// fuer alte Versionen von OCProp
							if (isset($_POST['submit']) && !isset($_POST['version2']))
							{
								$desc = iconv("ISO-8859-1", "UTF-8", $desc);
							}
						}

						if (isset($_POST['submitform']))  // Ocprop
						{
							// prüfen, ob sprache nicht schon vorhanden
							$rs = sql("SELECT COUNT(*) `count` FROM `cache_desc` WHERE `cache_id`='&1' AND `id` != '&2' AND `language`='&3'", $desc_record['cache_id'], $descid, $desclang);
							$r = sql_fetch_array($rs);
							if ($r['count'] > 0)
								tpl_errorMsg('editdesc', $error_desc_exists);
							mysql_free_result($rs);
						
							sql("UPDATE `cache_desc` SET 
    					            `desc_html`='&1',
    					            `desc_htmledit`='&2',
							            `desc`='&3', 
							            `short_desc`='&4', 
							            `hint`='&5',
							            `language`='&6'
							      WHERE `id`='&7'",
							            '1', 
							            (($desc_htmledit == 1) ? '1' : '0'),
							            (($desc_html == 1) ? $desc : nl2br($desc)),
							            $short_desc,
							            nl2br($hint),
							            $desclang,
							            $descid);

							// send notification on admin intervention
							if ($desc_record['user_id'] != $usr['userid'] &&
							    $opt['logic']['admin']['listingadmin_notification'] != '')
							{
								mail(
									$opt['logic']['admin']['listingadmin_notification'],
									mb_ereg_replace('{occode}', $desc_record['wp_oc'],
										mb_ereg_replace('{username}', $usr['username'],
										t('The cache description of {occode} has been modified by {username}'))),
										'');
							}

							// do not use slave server for the next time ...
							db_slave_exclude();

							// redirect to cachepage
							tpl_redirect('editcache.php?cacheid=' . urlencode($desc_record['cache_id']));
							exit;
						}
						else if (isset($_POST['show_all_langs']))
						{
							$show_all_langs = true;
						}
					}
					else
					{
						//here we read all used information from the DB
						$short_desc = strip_tags($desc_record['short_desc']);
						$hint = strip_tags($desc_record['hint']);
						$desc_htmledit = $desc_record['desc_htmledit'];
						$desc_lang = $desc_record['language'];

						$desc = $desc_record['desc'];
					}
					
					//here we only set up the template variables
					
					tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'), true);
			
					// ok ... die desclang zusammenbauen
					if ($show_all_langs == false)
					{
						$rs = sql("SELECT `show` FROM `languages_list_default` WHERE `show`='&1' AND `lang`='&2'", $desc_lang, $locale);
						if (mysql_num_rows($rs) == 0) $show_all_langs = true;
						sql_free_result($rs);
					}

					$languages = '';
					$rsLanguages = sql("SELECT `languages`.`short`, IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `name`
					                      FROM `languages`
					                 LEFT JOIN `languages_list_default` ON `languages`.`short`=`languages_list_default`.`show` AND `languages_list_default`.`lang`='&3'
					                 LEFT JOIN `sys_trans` ON `languages`.`trans_id`=`sys_trans`.`id`
					                 LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&3'
					                     WHERE `languages`.`short` 
					                              NOT IN (SELECT `language` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`!='&2') AND
					                           ('&4'=1 OR `languages_list_default`.`show`=`languages`.`short`)
					                  ORDER BY `name` ASC",
					                           $desc_record['cache_id'],
					                           $desc_lang,
					                           $locale,
					                           ($show_all_langs == true) ? 1 : 0);
					while ($rLanguage = sql_fetch_assoc($rsLanguages))
					{
						$sSelected = ($rLanguage['short'] == $desc_lang) ? ' selected="selected"' : '';
						$languages .= '<option value="' . $rLanguage['short'] . '"' . $sSelected . '>' . htmlspecialchars($rLanguage['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
					}
					sql_free_result($rsLanguages);

					tpl_set_var('desclangs', $languages);				
					
					if ($show_all_langs == false)
						tpl_set_var('show_all_langs_submit', $show_all_langs_submit);
					else
						tpl_set_var('show_all_langs_submit', '');
					
					tpl_set_var('show_all_langs_value', (($show_all_langs == false) ? 0 : 1));
					tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('hints', $hint);
					tpl_set_var('descid', $descid);
					tpl_set_var('cacheid', htmlspecialchars($desc_record['cache_id'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('desclang', htmlspecialchars($desc_lang, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('desclang_name', htmlspecialchars(db_LanguageFromShort($desc_lang), ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cachename', htmlspecialchars($desc_record['name'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('reset', $reset);  // obsolete
					tpl_set_var('submit', $submit);

					// Text / normal HTML / HTML editor
					$headers = tpl_get_var('htmlheaders') . "\n";
					if ($desc_htmledit == 1)
					{
						// TinyMCE
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
            $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/desc.js.php?cacheid=' .  ($desc_record['cache_id']+0) . '&lang=' .  strtolower($locale) . '"></script>' . "\n";
						tpl_set_var('descMode', 3);
					}
					else
						tpl_set_var('descMode', 2);
					$headers .= '<script language="javascript" type="text/javascript" src="templates2/ocstyle/js/editor.js"></script>' . "\n";
					tpl_set_var('htmlheaders', $headers);
				}
				else
				{
					//TODO: not the owner
				}
			}
			else
				tpl_errorMsg('editdesc', $error_desc_not_found);
		}
	}
	
	//make the template and send it out
	tpl_BuildTemplate();
?>
