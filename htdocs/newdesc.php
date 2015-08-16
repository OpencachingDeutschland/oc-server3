<?php
/***************************************************************************
																./newdesc.php
															-------------------
		begin                : July 7 2004

 		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	         
   Unicode Reminder メモ
                                				                                
	 add a cache description to a cache
	
	 used template(s): newdesc
	
 ****************************************************************************/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib2/OcHTMLPurifier.class.php');

	//Preprocessing
	if ($error == false)
	{
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}	

		//must be logged in
		if ($usr === false)
		{
			$tplname = 'login';

			tpl_set_var('username', '');
			tpl_set_var('target', htmlspecialchars('newdesc.php?cacheid=' . urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('message', $login_required);
			tpl_set_var('helplink', helppagelink('login'));
		}
		else
		{
			//user must be the owner of the cache
			$cache_rs = sql("SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			
			if (mysql_num_rows($cache_rs) > 0)
			{
				$cache_record = sql_fetch_array($cache_rs);
				mysql_free_result($cache_rs);
				
				if ($cache_record['user_id'] == $usr['userid'])
				{
					$tplname = 'newdesc';
					
					require($stylepath . '/newdesc.inc.php');
					
					//get the posted data
					$show_all_langs = isset($_POST['show_all_langs']) ? $_POST['show_all_langs'] : 0;
					$short_desc  = isset($_POST['short_desc']) ? $_POST['short_desc'] : '';  // Ocprop

					$hints = isset($_POST['hints']) ? $_POST['hints'] : '';  // Ocprop
					$sel_lang = isset($_POST['desc_lang']) ? $_POST['desc_lang'] : $default_lang;  // Ocprop
					$desc = isset($_POST['desc']) ? $_POST['desc'] : '';  // Ocprop

					// descMode auslesen, falls nicht gesetzt aus dem Profil laden
					if (isset($_POST['descMode']))  // Ocprop
					{
						$descMode = $_POST['descMode']+0;
						if (($descMode < 1) || ($descMode > 3)) $descMode = 3;
					}
					else
					{
						if (sqlValue("SELECT `no_htmledit_flag` FROM `user` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 1) == 1)
							$descMode = 2;
						else
							$descMode = 3;
					}

					if (($descMode < 1) || ($descMode > 3)) $descMode = 3;

					// fuer alte Versionen von OCProp
					if (isset($_POST['submit']) && !isset($_POST['version2']))
					{
						$descMode = (isset($_POST['desc_html']) && ($_POST['desc_html']==1)) ? 2 : 1;
						$_POST['submitform'] = $_POST['submit'];

						$desc = iconv("ISO-8859-1", "UTF-8", $desc);
						$short_desc = iconv("ISO-8859-1", "UTF-8", $short_desc);
						$hints = iconv("ISO-8859-1", "UTF-8", $hints);
					}

					if ($descMode != 1)
					{
            // Filter Input
            $purifier = new OcHTMLPurifier($opt);
            $desc = $purifier->purify($desc);

					}

					$desc_lang_exists = false;

					//save to db?
					if (isset($_POST['submitform']))  // Ocprop
					{
						//check if the entered language already exists
						$desc_rs = sql("SELECT `id` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $sel_lang);
						$desc_lang_exists = (mysql_num_rows($desc_rs) > 0);
						mysql_free_result($desc_rs);

						if ($desc_lang_exists == false)
						{
							//add to DB
							sql("INSERT INTO `cache_desc` ( 
														`id`, 
														`cache_id`, 
														`language`, 
														`desc`, 
														`desc_html`, 
														`desc_htmledit`, 
														`hint`, 
														`short_desc`,
														`last_modified`,
														`node`
													) VALUES ('', '&1', '&2', '&3', 1, '&4', '&5', '&6', NOW(), '&7')",
														$cache_id,
														$sel_lang,
														$desc,
														($descMode == 3) ? '1' : '0',
														nl2br(htmlspecialchars($hints, ENT_COMPAT, 'UTF-8')),
														$short_desc,
														$oc_nodeid);
						}

						// do not use slave server for the next time ...
						db_slave_exclude();

						tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
						exit;
					}
					elseif (isset($_POST['show_all_langs_submit']))
					{
						$show_all_langs = 1;
					}
					
					// check if any default language is available
					if ($show_all_langs == 0)
					{
						if (sqlValue("SELECT COUNT(*) 
						                FROM `languages_list_default` 
						           LEFT JOIN `cache_desc` ON `languages_list_default`.`show`=`cache_desc`.`language` AND `cache_desc`.`cache_id`='" . sql_escape($cache_id) . "'
						               WHERE `languages_list_default`.`lang`='" . sql_escape($locale) . "' AND ISNULL(`cache_desc`.`cache_id`)", 0) == 0)
						{
							$show_all_langs = 1;
						}
					}
					
					//build langslist
					$langoptions = '';
					$rsLanguages = sql("SELECT `short`, IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `name` 
					                      FROM `languages` 
					                 LEFT JOIN `languages_list_default` ON `languages`.`short`=`languages_list_default`.`show` AND `languages_list_default`.`lang`='&1'
					                 LEFT JOIN `sys_trans` ON `languages`.`trans_id`=`sys_trans`.`id`
					                 LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
					                     WHERE `languages`.`short` NOT IN (SELECT `language` FROM `cache_desc` WHERE `cache_id`='&3') AND 
					                           ('&2'=1 OR `languages_list_default`.`show`=`languages`.`short`)
					                  ORDER BY `name` ASC",
					                           $locale,
					                           (($show_all_langs == 1) ? 1 : 0),
					                           $cache_id);
					while ($rLanguage = sql_fetch_assoc($rsLanguages))
					{
						$sSelected = ($rLanguage['short'] == $sel_lang) ? ' selected="selected"' : '';
						$langoptions .= '<option value="' . htmlspecialchars($rLanguage['short'], ENT_COMPAT, 'UTF-8') . '"' . $sSelected . '>' . htmlspecialchars($rLanguage['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
					}
					sql_free_result($rsLanguages);
					if ($langoptions == '')
					{
						// We get here if someone has added descriptions for all avaiable languages, which
						// is very unlikely to happen ever. Just for completeness (see issue #108):
						tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
					}

					tpl_set_var('langoptions', $langoptions);

					//here we set the template vars
					tpl_set_var('name', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
					
					tpl_set_var('lang_message', $desc_lang_exists ? $lang_message : '');
					
					tpl_set_var('show_all_langs', $show_all_langs);
					tpl_set_var('show_all_langs_submit', ($show_all_langs == 0) ? $show_all_langs_submit : '');
					tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('hints', htmlspecialchars($hints, ENT_COMPAT, 'UTF-8'));

					// plain HTML or Wysiwyg editor
					tpl_set_var('descMode', $descMode);
					$headers = tpl_get_var('htmlheaders') . "\n";
					if ($descMode == 3)
					{
						// TinyMCE
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/desc.js.php?cacheid=' . ($cache_id+0) . '&lang=' .  strtolower($locale) . '"></script>' . "\n";
					}
					$headers .= '<script language="javascript" type="text/javascript" src="templates2/ocstyle/js/editor.js"></script>' . "\n";
					tpl_set_var('htmlheaders', $headers);

					tpl_set_var('reset', $reset);  // obsolete
					tpl_set_var('submit', $submit);
				}
				else
				{
					//TODO: not the owner
				}
			}
			else
			{
				mysql_free_result($cache_rs);
				//TODO: cache not exist
			}
		}
	}
	
	//make the template and send it out
	tpl_BuildTemplate();
?>
