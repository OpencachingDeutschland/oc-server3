<?php
/***************************************************************************
																./newcache.php
															-------------------
		begin                : June 24 2004

 		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 submitt a new cache

	 used template(s): newcache, viewcache, login

 ****************************************************************************/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once('./lib2/OcHTMLPurifier.class.php');

	$no_tpl_build = false;

	//Preprocessing
	if ($error == false)
	{
		//must be logged in
		if ($usr === false)
		{
			$tplname = 'login';

			tpl_set_var('username', '');
			tpl_set_var('target', 'newcache.php');
			tpl_set_var('message_start', "");
			tpl_set_var('message_end', "");
			tpl_set_var('message', $login_required);
			tpl_set_var('helplink', helppagelink('login'));
		}
		else
		{
			$errors = false; // set if there was any errors

			//set here the template to process
			$tplname = 'newcache';
			require_once($stylepath . '/' . $tplname . '.inc.php');

			//set template replacements
			tpl_set_var('reset', $reset);  // obsolete
			tpl_set_var('submit', $submit);
			tpl_set_var('general_message', '');
			tpl_set_var('hidden_since_message', '');
			tpl_set_var('activate_on_message', '');
			tpl_set_var('lon_message', '');
			tpl_set_var('lat_message', '&nbsp;&nbsp;');
			tpl_set_var('tos_message', '');
			tpl_set_var('name_message', '');
			tpl_set_var('desc_message', '');
			tpl_set_var('effort_message', '');
			tpl_set_var('size_message', '');
			tpl_set_var('wpgc_message', '');
			tpl_set_var('type_message', '');
			tpl_set_var('diff_message', '');
			tpl_set_var('safari_message', '');

			$sel_type = isset($_POST['type']) ? $_POST['type'] : 0;  // Ocprop
			if (!isset($_POST['size']))
			{
				if ($sel_type == 4 || $sel_type == 5)
				{
					$sel_size = 7;
				}
				else
				{
					$sel_size = -1;
				}
			}
			else
			{
				$sel_size = isset($_POST['size']) ? $_POST['size'] : -1;  // Ocprop
			}
			$sel_lang = isset($_POST['desc_lang']) ? $_POST['desc_lang'] : $default_lang;
			$sel_country = isset($_POST['country']) ? $_POST['country'] : getUserCountry();  // Ocprop
			$show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries'] : 0;
			$show_all_langs = isset($_POST['show_all_langs']) ? $_POST['show_all_langs'] : 0;

			//coords
			$lonEW = isset($_POST['lonEW']) ? $_POST['lonEW'] : $default_EW;  // Ocprop
			if ($lonEW == 'E')
			{
				tpl_set_var('lonEsel', ' selected="selected"');
				tpl_set_var('lonWsel', '');
			}
			else
			{
				tpl_set_var('lonEsel', '');
				tpl_set_var('lonWsel', ' selected="selected"');
			}
			$lon_h = isset($_POST['lon_h']) ? $_POST['lon_h'] : '0';  // Ocprop
			tpl_set_var('lon_h', htmlspecialchars($lon_h, ENT_COMPAT, 'UTF-8'));

			$lon_min = isset($_POST['lon_min']) ? $_POST['lon_min'] : '00.000';  // Ocprop
			tpl_set_var('lon_min', htmlspecialchars($lon_min, ENT_COMPAT, 'UTF-8'));

			$latNS = isset($_POST['latNS']) ? $_POST['latNS'] : $default_NS;  // Ocprop
			if ($latNS == 'N')
			{
				tpl_set_var('latNsel', ' selected="selected"');
				tpl_set_var('latSsel', '');
			}
			else
			{
				tpl_set_var('latNsel', '');
				tpl_set_var('latSsel', ' selected="selected"');
			}
			$lat_h = isset($_POST['lat_h']) ? $_POST['lat_h'] : '0';  // Ocprop
			tpl_set_var('lat_h', htmlspecialchars($lat_h, ENT_COMPAT, 'UTF-8'));

			$lat_min = isset($_POST['lat_min']) ? $_POST['lat_min'] : '00.000';  // Ocprop
			tpl_set_var('lat_min', htmlspecialchars($lat_min, ENT_COMPAT, 'UTF-8'));

			//name
			$name = isset($_POST['name']) ? trim($_POST['name']) : '';  // Ocprop
			tpl_set_var('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));

			//shortdesc
			$short_desc = isset($_POST['short_desc']) ? $_POST['short_desc'] : '';
			tpl_set_var('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));

			//desc
			$desc = isset($_POST['desc']) ? $_POST['desc'] : '';
			tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));

			// descMode auslesen, falls nicht gesetzt aus dem Profil laden
			if (isset($_POST['descMode']))  // Ocprop
				$descMode = $_POST['descMode']+0;
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
					$descMode = (isset($_POST['desc_html']) && ($_POST['desc_html']==1)) ? 2 : 1;  // Ocprop
					$_POST['submitform'] = $_POST['submit'];

					$short_desc = iconv("ISO-8859-1", "UTF-8", $short_desc);
					$desc = iconv("ISO-8859-1", "UTF-8", $desc);
					$name = iconv("ISO-8859-1", "UTF-8", $name);
			}

			// normal HTML / HTML editor
			tpl_set_var('descMode', $descMode);
			$headers = tpl_get_var('htmlheaders') . "\n";

			if ($descMode == 3)
			{
				// TinyMCE
				$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
        $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/desc.js.php?cacheid=0&lang='.strtolower($locale).'"></script>' . "\n";
			}
			$headers .= '<script language="javascript" type="text/javascript" src="templates2/ocstyle/js/editor.js"></script>' . "\n";
			tpl_set_var('htmlheaders', $headers);

			//effort
			$search_time = isset($_POST['search_time']) ? $_POST['search_time'] : '0';
			$way_length = isset($_POST['way_length']) ? $_POST['way_length'] : '0';

			$search_time = mb_ereg_replace(',', '.', $search_time);
			$way_length = mb_ereg_replace(',', '.', $way_length);

			if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3)
			{
				$st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
				$st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

				if (is_numeric($st_hours) && is_numeric($st_minutes))
				{
					if (($st_minutes >= 0) && ($st_minutes < 60))
					{
						$search_time = $st_hours + $st_minutes / 60;
					}
				}
			}

			$st_hours = floor($search_time);
			$st_minutes = sprintf('%02.0F', ($search_time - $st_hours) * 60);

			tpl_set_var('search_time', $st_hours . ':' . $st_minutes);
			tpl_set_var('way_length', $way_length);


			//hints
			$hints = isset($_POST['hints']) ? $_POST['hints'] : '';
			tpl_set_var('hints', htmlspecialchars($hints, ENT_COMPAT, 'UTF-8'));

			// fuer alte Versionen von OCProp
			if (isset($_POST['submit']) && !isset($_POST['version2']))
			{
					$hints = iconv("ISO-8859-1", "UTF-8", $hints);
			}

			//tos
			$tos = isset($_POST['TOS']) ? 1 : 0;  // Ocprop
			if ($tos == 1)
				tpl_set_var('toschecked', ' checked="checked"');
			else
				tpl_set_var('toschecked', '');

			//hidden_since
			$hidden_day = isset($_POST['hidden_day']) ? $_POST['hidden_day'] : date('d');  // Ocprop
			$hidden_month = isset($_POST['hidden_month']) ? $_POST['hidden_month'] : date('m');  // Ocprop
			$hidden_year = isset($_POST['hidden_year']) ? $_POST['hidden_year'] : date('Y');  // Ocprop
			tpl_set_var('hidden_day', htmlspecialchars($hidden_day, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('hidden_month', htmlspecialchars($hidden_month, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('hidden_year', htmlspecialchars($hidden_year, ENT_COMPAT, 'UTF-8'));

			//activation date
			$activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date('d');
			$activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date('m');
			$activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date('Y');
			tpl_set_var('activate_day', htmlspecialchars($activate_day, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('activate_month', htmlspecialchars($activate_month, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('activate_year', htmlspecialchars($activate_year, ENT_COMPAT, 'UTF-8'));

			tpl_set_var('publish_now_checked', '');
			tpl_set_var('publish_later_checked', '');
			tpl_set_var('publish_notnow_checked', '');

			$publish = isset($_POST['publish']) ? $_POST['publish'] : 'now2';  // Ocprop
			if($publish == 'now2')
			{
				tpl_set_var('publish_now_checked', 'checked');
			}
			else if($publish == 'later')
			{
				tpl_set_var('publish_later_checked', 'checked');
			}
			else // notnow
			{
				$publish = 'notnow';
				tpl_set_var('publish_notnow_checked', 'checked');
			}

			// fill activate hours
			$activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] + 0 : date('H') + 0;
			$activation_hours = '';
			for ($i = 0; $i <= 23; $i++)
			{
				if ($activate_hour == $i)
				{
					$activation_hours .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
				}
				else
				{
					$activation_hours .= '<option value="' . $i . '">' . $i . '</option>';
				}
				$activation_hours .= "\n";
			}
			tpl_set_var('activation_hours', $activation_hours);

			//log-password
			$log_pw = isset($_POST['log_pw']) ? mb_substr($_POST['log_pw'], 0, 20) : '';
			tpl_set_var('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));

			// gc- and nc-waypoints
			// fix #4356: gc waypoints are frequently copy&pasted with leading spaces
			$wp_gc = isset($_POST['wp_gc']) ? strtoupper(trim($_POST['wp_gc'])) : '';  // Ocprop
			tpl_set_var('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));

			$wp_nc = isset($_POST['wp_nc']) ? strtoupper(trim($_POST['wp_nc'])) : '';
			tpl_set_var('wp_nc', htmlspecialchars($wp_nc, ENT_COMPAT, 'UTF-8'));

			//difficulty
			$difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : 1;  // Ocprop
			$difficulty_options = '<option value="1">'.$sel_message.'</option>';
			for ($i = 2; $i <= 10; $i++)
			{
				if ($difficulty == $i)
				{
					$difficulty_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
				}
				else
				{
					$difficulty_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
				}
				$difficulty_options .= "\n";
			}
			tpl_set_var('difficulty_options', $difficulty_options);

			//terrain
			$terrain = isset($_POST['terrain']) ? $_POST['terrain'] : 1;  // Ocprop
			$terrain_options = '<option value="1">'.$sel_message.'</option>';;
			for ($i = 2; $i <= 10; $i++)
			{
				if ($terrain == $i)
				{
					$terrain_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
				}
				else
				{
					$terrain_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
				}
				$terrain_options .= "\n";
			}
			tpl_set_var('terrain_options', $terrain_options);

			//sizeoptions
			$sSelected = ($sel_size == -1) ? ' selected="selected"' : '';
			$sizes = '<option value="-1"' . $sSelected . '>' . htmlspecialchars(t('Please select!'), ENT_COMPAT, 'UTF-8') . '</option>';
			$rsSizes = sql("SELECT `cache_size`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_size`.`name`) AS `name` 
			                  FROM `cache_size` 
			             LEFT JOIN `sys_trans` ON `cache_size`.`trans_id`=`sys_trans`.`id` 
			             LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND 
			                       `sys_trans_text`.`lang`='" . sql_escape($locale) . "' 
			              ORDER BY `cache_size`.`ordinal` ASC");
			while ($rSize = sql_fetch_assoc($rsSizes))
			{
				$sSelected = ($rSize['id'] == $sel_size) ? ' selected="selected"' : '';
				$sizes .= '<option value="' . $rSize['id'] . '"' . $sSelected . '>' . htmlspecialchars($rSize['name'], ENT_COMPAT, 'UTF-8') . '</option>';
			}
			sql_free_result($rsSizes);
			tpl_set_var('sizeoptions', $sizes);

			//typeoptions
			$sSelected = ($sel_type == -1) ? ' selected="selected"' : '';
			$types = '<option value="-1"' . $sSelected . '>' . htmlspecialchars(t('Please select!'), ENT_COMPAT, 'UTF-8') . '</option>';
			$rsTypes = sql("SELECT `cache_type`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_type`.`name`) AS `name` 
			                  FROM `cache_type` 
			             LEFT JOIN `sys_trans` ON `cache_type`.`trans_id`=`sys_trans`.`id` 
			             LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND 
			                       `sys_trans_text`.`lang`='" . sql_escape($locale) . "' 
			              ORDER BY `cache_type`.`ordinal` ASC");
			while ($rType = sql_fetch_assoc($rsTypes))
			{
				$sSelected = ($rType['id'] == $sel_type) ? ' selected="selected"' : '';
				$types .= '<option value="' . $rType['id'] . '"' . $sSelected . '>' . htmlspecialchars($rType['name'], ENT_COMPAT, 'UTF-8') . '</option>';
			}
			sql_free_result($rsTypes);
			tpl_set_var('typeoptions', $types);

			if (isset($_POST['show_all_countries_submit']))
			{
				$show_all_countries = 1;
			}
			elseif (isset($_POST['show_all_langs_submit']))
			{
				$show_all_langs = 1;
			}

			//langoptions
			$langsoptions = '';

			//check if selected country is in list_default
			if ($show_all_langs == 0)
			{
				$rs = sql("SELECT `show` FROM `languages_list_default` WHERE `show`='&1' AND `lang`='&2'", $sel_lang, $locale);
				if (mysql_num_rows($rs) == 0) $show_all_langs = 1;
				sql_free_result($rs);
			}

			if ($show_all_langs == 0)
			{
				tpl_set_var('show_all_langs', '0');
				tpl_set_var('show_all_langs_submit', '<input type="submit" name="show_all_langs_submit" value="' . $show_all . '"/>');

				$rs = sql("SELECT `languages`.`short`, IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `name` FROM `languages` INNER JOIN `languages_list_default` ON `languages`.`short`=`languages_list_default`.`show` LEFT JOIN `sys_trans` ON `languages`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' WHERE `languages_list_default`.`lang`='&1' ORDER BY `name` ASC", $locale);
			}
			else
			{
				tpl_set_var('show_all_langs', '1');
				tpl_set_var('show_all_langs_submit', '');

				$rs = sql("SELECT `languages`.`short`, IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `name` FROM `languages` LEFT JOIN `sys_trans` ON `languages`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `name` ASC", $locale);
			}

			while ($record = sql_fetch_assoc($rs))
			{
				$sSelected = ($record['short'] == $sel_lang) ? ' selected="selected"' : '';
				$langsoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '"' . $sSelected . '>' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
			}

			tpl_set_var('langoptions', $langsoptions);

			//countryoptions
			$countriesoptions = '';

			//check if selected country is in list_default
			if ($show_all_countries == 0)
			{
				$rs = sql("SELECT `show` FROM `countries_list_default` WHERE `show`='&1' AND `lang`='&2'", $sel_country, $locale);
				if (mysql_num_rows($rs) == 0) $show_all_countries = 1;
				sql_free_result($rs);
			}

			if ($show_all_countries == 0)
			{
				tpl_set_var('show_all_countries', '0');
				tpl_set_var('show_all_countries_submit', '<input type="submit" id="showallcountries" class="formbutton" name="show_all_countries_submit" value="' . $show_all . '" onclick="submitbutton(\'showallcountries\')" />');

				$rs = sql("SELECT `countries`.`short`, IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name` FROM `countries` INNER JOIN `countries_list_default` ON `countries_list_default`.`show`=`countries`.`short` LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' WHERE `countries_list_default`.`lang`='&1' ORDER BY `name` ASC", $locale);
			}
			else
			{
				tpl_set_var('show_all_countries', '1');
				tpl_set_var('show_all_countries_submit', '');

				$rs = sql("SELECT `countries`.`short`, IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name` FROM `countries` LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `name` ASC", $locale);
			}

			// $opt['locale'][$locale]['country'] would give country of chosen langugage
			// build the "country" dropdown list, preselect $sel_country
			while ($record = sql_fetch_array($rs))
			{
				$sSelected = ($record['short'] == $sel_country) ? ' selected="selected"' : '';
				$countriesoptions .= '<option value="' . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8') . '"' . $sSelected . '>' . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
			}
			sql_free_result($rs);

			tpl_set_var('countryoptions', $countriesoptions);

			// cache-attributes
			$cache_attribs = isset($_POST['cache_attribs']) ? mb_split(';', $_POST['cache_attribs']) : array();

			// cache-attributes
			$bBeginLine = true;
			$nPrevLineAttrCount = 0;
			$nLineAttrCount = 0;

			$cache_attrib_list = '';
			$cache_attrib_array = '';
			$cache_attribs_string = '';

			$rsAttrGroup = sql("SELECT `attribute_groups`.`id`, IFNULL(`sys_trans_text`.`text`, `attribute_groups`.`name`) AS `name`, `attribute_categories`.`color` 
			                      FROM `attribute_groups` 
			                INNER JOIN `attribute_categories` ON `attribute_groups`.`category_id`=`attribute_categories`.`id` 
			                 LEFT JOIN `sys_trans` ON `attribute_groups`.`trans_id`=`sys_trans`.`id`
			                 LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
			                  ORDER BY `attribute_groups`.`category_id` ASC, `attribute_groups`.`id` ASC", $locale);
			while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup))
			{
				$group_line = '';

				$rs = sql("SELECT `cache_attrib`.`id`, IFNULL(`ttname`.`text`, `cache_attrib`.`name`) AS `name`, `cache_attrib`.`icon_undef`, `cache_attrib`.`icon_large`, IFNULL(`ttdesc`.`text`, `cache_attrib`.`html_desc`) AS `html_desc` 
				             FROM `cache_attrib` 
		            LEFT JOIN `sys_trans` AS `tname` ON `cache_attrib`.`trans_id`=`tname`.`id` AND `cache_attrib`.`name`=`tname`.`text`
		            LEFT JOIN `sys_trans_text` AS `ttname` ON `tname`.`id`=`ttname`.`trans_id` AND `ttname`.`lang`='&1'
		            LEFT JOIN `sys_trans` AS `tdesc` ON `cache_attrib`.`html_desc_trans_id`=`tdesc`.`id` AND `cache_attrib`.`html_desc`=`tdesc`.`text`
		            LEFT JOIN `sys_trans_text` AS `ttdesc` ON `tdesc`.`id`=`ttdesc`.`trans_id` AND `ttdesc`.`lang`='&1'
				            WHERE `cache_attrib`.`group_id`=" . ($rAttrGroup['id']+0) . " AND
							NOT IFNULL(`cache_attrib`.`hidden`, 0)=1 AND 
							 `cache_attrib`.`selectable`!=0 ORDER BY `cache_attrib`.`group_id`, `cache_attrib`.`id`", $locale);
				while($record = sql_fetch_array($rs))
				{
					$line = $cache_attrib_pic;

					$line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
					$line = mb_ereg_replace('{attrib_text}', escape_javascript($record['name']), $line);
					if (in_array($record['id'], $cache_attribs))
						$line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
					else
						$line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
					$line = mb_ereg_replace('{html_desc}', escape_javascript($record['html_desc']), $line);
					$line = mb_ereg_replace('{name}', escape_javascript($record['name']), $line);
					$line = mb_ereg_replace('{color}', $rAttrGroup['color'], $line);
					$group_line .= $line;
					$nLineAttrCount++;

					$line = $cache_attrib_js;
					$line = mb_ereg_replace('{id}', $record['id'], $line);
					if (in_array($record['id'], $cache_attribs))
						$line = mb_ereg_replace('{selected}', 1, $line);
					else
						$line = mb_ereg_replace('{selected}', 0, $line);
					$line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
					$line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);
					if ($cache_attrib_array != '') $cache_attrib_array .= ',';
					$cache_attrib_array .= $line;

					if (in_array($record['id'], $cache_attribs))
					{
						if ($cache_attribs_string != '') $cache_attribs_string .= ';';
						$cache_attribs_string .= $record['id'];
					}
				}
				sql_free_result($rs);

				if ($group_line != '')
				{
					$group_img = $cache_attrib_group;
					$group_img = mb_ereg_replace('{color}', $rAttrGroup['color'], $group_img);
					$group_img = mb_ereg_replace('{attribs}', $group_line, $group_img);
					$group_img = mb_ereg_replace('{name}', htmlspecialchars($rAttrGroup['name'], ENT_COMPAT, 'UTF-8'), $group_img);

					if ($bBeginLine == true)
					{
						$cache_attrib_list .= '<div class="attribswide">';
						$bBeginLine = false;
					}

					$cache_attrib_list .= $group_img;
					$nPrevLineAttrCount += $nLineAttrCount;

					$nLineAttrCount = 0;
				}
			}
			sql_free_result($rsAttrGroup);
			if ($bBeginLine == false)
				$cache_attrib_list .= '</div>';

			tpl_set_var('cache_attrib_list', $cache_attrib_list);
			tpl_set_var('jsattributes_array', $cache_attrib_array);
			tpl_set_var('cache_attribs', $cache_attribs_string);

			if (isset($_POST['submitform']))  // Ocprop
			{
				//check the entered data

				//check coordinates
				if ($lat_h!='' || $lat_min!='')
				{
					if (!mb_ereg_match('^[0-9]{1,2}$', $lat_h))
					{
						tpl_set_var('lat_message', $error_lat_not_ok);
						$error = true;
						$lat_h_not_ok = true;
					}
					else
					{
						if (($lat_h >= 0) && ($lat_h < 90))
						{
							$lat_h_not_ok = false;
						}
						else
						{
							tpl_set_var('lat_message', $error_lat_not_ok);
							$error = true;
							$lat_h_not_ok = true;
						}
					}

					if (is_numeric($lat_min))
					{
						if (($lat_min >= 0) && ($lat_min < 60))
						{
							$lat_min_not_ok = false;
						}
						else
						{
							tpl_set_var('lat_message', $error_lat_not_ok);
							$error = true;
							$lat_min_not_ok = true;
						}
					}
					else
					{
						tpl_set_var('lat_message', $error_lat_not_ok);
						$error = true;
						$lat_min_not_ok = true;
					}

					$latitude = $lat_h + $lat_min / 60;
					if ($latNS == 'S') $latitude = -$latitude;

					if ($latitude == 0)
					{
						tpl_set_var('lat_message', $error_lat_not_ok);
						$error = true;
						$lat_min_not_ok = true;
					}
				}
				else
				{
					tpl_set_var('lat_message', $error_lat_not_ok);
					$lat_h_not_ok = true;
					$lat_min_not_ok = true;
				}

				if ($lon_h!='' || $lon_min!='')
				{
					if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h))
					{
						tpl_set_var('lon_message', $error_long_not_ok);
						$error = true;
						$lon_h_not_ok = true;
					}
					else
					{
						if (($lon_h >= 0) && ($lon_h < 180))
						{
							$lon_h_not_ok = false;
						}
						else
						{
							tpl_set_var('lon_message', $error_long_not_ok);
							$error = true;
							$lon_h_not_ok = true;
						}
					}

					if (is_numeric($lon_min))
					{
						if (($lon_min >= 0) && ($lon_min < 60))
						{
							$lon_min_not_ok = false;
						}
						else
						{
							tpl_set_var('lon_message', $error_long_not_ok);
							$error = true;
							$lon_min_not_ok = true;
						}
					}
					else
					{
						tpl_set_var('lon_message', $error_long_not_ok);
						$error = true;
						$lon_min_not_ok = true;
					}

					$longitude = $lon_h + $lon_min / 60;
					if ($lonEW == 'W') $longitude = -$longitude;

					if ($longitude == 0)
					{
						tpl_set_var('lon_message', $error_long_not_ok);
						$error = true;
						$lon_min_not_ok = true;
					}
				}
				else
				{
					tpl_set_var('lon_message', $error_long_not_ok);
					$lon_h_not_ok = true;
					$lon_min_not_ok = true;
				}

				$lon_not_ok = $lon_min_not_ok || $lon_h_not_ok;
				$lat_not_ok = $lat_min_not_ok || $lat_h_not_ok;

				// check for duplicate coords
				if (!($lon_not_ok || $lat_not_ok))
				{
					$duplicate_wpoc =
					  sqlValue("SELECT MIN(wp_oc) FROM `caches`
						                           WHERE `status`=1
						                             AND ROUND(`longitude`,6)=ROUND('" . sql_escape($longitude) . "',6)
						                             AND ROUND(`latitude`,6)=ROUND('" . sql_escape($latitude) . "',6)", null);
					if ($duplicate_wpoc)
					{
						tpl_set_var('lon_message', mb_ereg_replace('%1', $duplicate_wpoc, $error_duplicate_coords));
						$lon_not_ok = true;
					}
				}

				//check effort
				$time_not_ok = true;
				if (is_numeric($search_time) || ($search_time == ''))
				  {
				    $time_not_ok = false;
				  }
				if ($time_not_ok)
				  {
				    tpl_set_var('effort_message', $time_not_ok_message);
				    $error = true;
				  }
				$way_length_not_ok =true;
				if  (is_numeric($way_length) || ($search_time == ''))
				  {
				    $way_length_not_ok = false;
				  }
				if ($way_length_not_ok)
				  {
				    tpl_set_var('effort_message', $way_length_not_ok_message);
				    $error = true;
				  }


				//check hidden_since
				$hidden_date_not_ok = true;
				if (is_numeric($hidden_day) && is_numeric($hidden_month) && is_numeric($hidden_year))
				{
					$hidden_date_not_ok = (checkdate($hidden_month, $hidden_day, $hidden_year) == false);
				}
				if ($hidden_date_not_ok)
				{
					tpl_set_var('hidden_since_message', $date_not_ok_message);
					$error = true;
				}

				//check GC waypoint
				$wpgc_not_ok = $wp_gc != "" && !preg_match("/^(?:GC|CX)[0-9A-Z]{3,6}$/", $wp_gc);
				if ($wpgc_not_ok)
				{
					tpl_set_var('wpgc_message', $bad_wpgc_message);
					$error = true;
				}

				//check date_activate
				$activation_date_not_ok = true;
				if (is_numeric($activate_day) && is_numeric($activate_month) && is_numeric($activate_year) && is_numeric($activate_hour))
				{
					$activation_date_not_ok = ((checkdate($activate_month, $activate_day, $activate_year) == false) || $activate_hour < 0 || $activate_hour > 23);
				}
				if ($activation_date_not_ok == false)
				{
					if(!($publish == 'now2' || $publish == 'later' || $publish == 'notnow'))
					{
						$activation_date_not_ok = true;
					}
				}
				if ($activation_date_not_ok)
				{
					tpl_set_var('activate_on_message', $date_not_ok_message);
					$error = true;
				}

				//name
				if ($name == '')
				{
					tpl_set_var('name_message', $name_not_ok_message);
					$error = true;
					$name_not_ok = true;
				}
				else
				{
					$name_not_ok = false;
				}

				//tos
				if ($tos != 1)
				{
					tpl_set_var('tos_message', $tos_not_ok_message);
					$error = true;
					$tos_not_ok = true;
				}
				else
				{
					$tos_not_ok = false;
				}

        // Filter Input
        $purifier = new OcHTMLPurifier($opt);
        $desc = $purifier->purify($desc);
				tpl_set_var('desc', htmlspecialchars($desc, ENT_COMPAT, 'UTF-8'));

				//cache-size
				$size_not_ok = false;
				if ($sel_size == -1)
				{
					tpl_set_var('size_message', $size_not_ok_message);
					$error = true;
					$size_not_ok = true;
				}

				//cache-type
				$type_not_ok = false;
				if ($sel_type == -1)
				{
					tpl_set_var('type_message', $type_not_ok_message);
					$error = true;
					$type_not_ok = true;
				}

				if ($sel_size != 7 && ($sel_type == 4 || $sel_type == 5))
				{
					if (!$size_not_ok) tpl_set_var('size_message', $sizemismatch_message);
					$error = true;
					$size_not_ok = true;
				}

				//difficulty / terrain
				$diff_not_ok = false;
				if ($difficulty < 2 || $difficulty > 10 || $terrain < 2 || $terrain > 10)
				{
					tpl_set_var('diff_message', $diff_not_ok_message);
					$error = true;
					$diff_not_ok = true;
				}

				// attributes
				$attribs_not_ok = false;
				if (in_array(ATTRIB_ID_SAFARI,$cache_attribs) && $sel_type != 4)
				{
					tpl_set_var('safari_message', $safari_not_allowed_message);
					$error = true;
					$attribs_not_ok = true;
				}
				else
					tpl_set_var('safari_message', '');

				//no errors?
				if (!($tos_not_ok || $name_not_ok || $hidden_date_not_ok || $activation_date_not_ok || $lon_not_ok || $lat_not_ok || $time_not_ok || $way_length_not_ok || $size_not_ok || $type_not_ok || $diff_not_ok || $attribs_not_ok || $wpgc_not_ok))
				{
					//sel_status
					$now = getdate();
					$today = mktime(0, 0, 0, $now['mon'], $now['mday'], $now['year']);
					$hidden_date = mktime(0, 0, 0, $hidden_month, $hidden_day, $hidden_year);

					if (($hidden_date > $today) && ($sel_type != 6))
					{
						$sel_status = 2; //currently not available
					}
					else
					{
						$sel_status = 1; //available
					}

					if($publish == 'now2')
					{
						$activation_date = 'NULL';
						$activation_column = ' ';
					}
					elseif($publish == 'later')
					{
						$sel_status = 5;
						$activation_date = "'".date('Y-m-d H:i:s', mktime($activate_hour, 0, 0, $activate_month, $activate_day, $activate_year))."'";
					}
					elseif($publish == 'notnow')
					{
						$sel_status = 5;
						$activation_date = 'NULL';
					}
					else
					{
						// should never happen
						$activation_date = 'NULL';
					}

					//add record to caches table
					sql("INSERT INTO `caches` (
												`cache_id`,
												`user_id`,
												`name`,
												`longitude`,
												`latitude`,
												`type` ,
												`status` ,
												`country` ,
												`date_hidden` ,
												`date_activate` ,
												`size` ,
												`difficulty` ,
												`terrain`,
												`logpw`,
												`search_time`,
												`way_length`,
												`wp_gc`,
												`wp_nc`,
												`node`
											) VALUES (
												'', '&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', $activation_date, 
												'&9', '&10', '&11', '&12', '&13', '&14', '&15', '&16', '&17')",
											$usr['userid'],
											$name,
											$longitude,
											$latitude,
											$sel_type,
											$sel_status,
											$sel_country,
											date('Y-m-d', $hidden_date),
											$sel_size,
											$difficulty,
											$terrain,
											$log_pw,
											$search_time,
											$way_length,
											$wp_gc,
											$wp_nc,
											$oc_nodeid);
					$cache_id = mysql_insert_id($dblink);

					// do not use slave server for the next time ...
					db_slave_exclude();

					//add record to cache_desc table
					if ($descMode != 1)
					{
						sql("INSERT INTO `cache_desc` (
													`id`,
													`cache_id`,
													`language`,
													`desc`,
													`desc_html`,
													`hint`,
													`short_desc`,
													`last_modified`,
													`desc_htmledit`,
													`node`
												) VALUES ('', '&1', '&2', '&3', '1', '&4', '&5', NOW(), '&6', '&7')",
												$cache_id,
												$sel_lang,
												$desc,
												nl2br(htmlspecialchars($hints, ENT_COMPAT, 'UTF-8')),
												$short_desc,
												(($descMode == 3) ? 1 : 0),
												$oc_nodeid);
					}

					// insert cache-attributes
					for($i=0; $i<count($cache_attribs); $i++)
					{
						if(($cache_attribs[$i]+0) > 0)
						{
							sql("INSERT INTO `caches_attributes` (`cache_id`, `attrib_id`) VALUES ('&1', '&2')", $cache_id, $cache_attribs[$i]+0);
						}
					}

					// only if cache is published NOW or activate_date is in the past
					if($publish == 'now2' || ($publish == 'later' && mktime($activate_hour, 0, 0, $activate_month, $activate_day, $activate_year) <= $today))
					{
						//do event handling
						include_once($opt['rootpath'] . '/lib/eventhandler.inc.php');

						event_notify_new_cache($cache_id + 0);
						event_new_cache($usr['userid']+0);
					}

					// redirection
					tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
				}
				else
				{
					tpl_set_var('general_message', $error_general);
				}
			}
		}
	}

	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate();
	}
?>
