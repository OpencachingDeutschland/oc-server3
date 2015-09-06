<?php
/****************************************************************************
															./removedesc.php
															-------------------
		begin                : July 7 2004

 		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	                
   Unicode Reminder メモ
                         				                                
	 remove a cache description
	 
 ****************************************************************************/
 
   //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');

	//Preprocessing
	if ($error == false)
	{
		//cacheid
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}
		$desclang = '';
		if (isset($_REQUEST['desclang']))
		{
			$desclang = $_REQUEST['desclang'];
		}
		$remove_commit = 0;
		if (isset($_REQUEST['commit']))
		{
			$remove_commit = $_REQUEST['commit'];
		}

		if ($usr === false)
		{
			$tplname = 'login';
			
			tpl_set_var('username', '');
			tpl_set_var('target', htmlspecialchars('removedesc.php?cacheid=' . urlencode($cache_id) . '&desclang=' . urlencode($desclang), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('message_start', '');
			tpl_set_var('message_end', '');
			tpl_set_var('message', $login_required);
			tpl_set_var('helplink', helppagelink('login'));
		}
		else
		{
			$cache_rs = sql("SELECT `user_id`, `name` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
			$cache_record = sql_fetch_array($cache_rs);
			sql_free_result($cache_rs);

			if ($cache_record !== false)
			{
				if ($cache_record['user_id'] == $usr['userid'])
				{
					$desc_rs = sql("SELECT `id`, `uuid`, `node` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $desclang);
					if (mysql_num_rows($desc_rs) == 1)
					{
						$desc_record = sql_fetch_array($desc_rs);
						mysql_free_result($desc_rs);
						require($stylepath . '/removedesc.inc.php');

						if ($desc_record['node'] != $oc_nodeid)
						{
							tpl_errorMsg('removedesc', $error_wrong_node);
							exit;
						}

						if ($remove_commit == 1)
						{
							//remove it from cache_desc
							sql("DELETE FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'", $cache_id, $desclang);

							// do not use slave server for the next time ...
							db_slave_exclude();

							tpl_redirect('editcache.php?cacheid=' . urlencode($cache_id));
							exit;
						}
						else
						{
							//commit the removement
							$tplname = 'removedesc';
							
							tpl_set_var('desclang_name', db_LanguageFromShort($desclang));
							tpl_set_var('cachename', htmlspecialchars($cache_record['name'], ENT_COMPAT, 'UTF-8'));
							tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
							tpl_set_var('desclang_urlencode', htmlspecialchars(urlencode($desclang), ENT_COMPAT, 'UTF-8'));
						}
					}
					else
					{
						//TODO: desc not exist
					}
				}
				else
				{
					//TODO: not the owner
				}
			}
			else
			{
				//TODO: cache not exist
			}
		}
	}
	
	//make the template and send it out
	tpl_BuildTemplate();

?>