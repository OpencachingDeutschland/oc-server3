<?php
/****************************************************************************
															./removelogs.php
															-------------------
		begin                : July 7 2004

 		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 remove a cache log

	 GET/POST-Parameter: logid

 ****************************************************************************/

   //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require_once($stylepath . '/lib/icons.inc.php');

	//Preprocessing
	if ($error == false)
	{
		//cacheid
		$log_id = 0;
		if (isset($_REQUEST['logid']))
		{
			$log_id = $_REQUEST['logid'];
		}

		if ($usr === false)
		{
			$tplname = 'login';

			tpl_set_var('username', '');
			tpl_set_var('target', htmlspecialchars('removelog.php?logid=' . urlencode($log_id), ENT_COMPAT, 'UTF-8'));
			tpl_set_var('message', $login_required);
		}
		else
		{
			$log_rs = sql("SELECT	`cache_logs`.`node` AS `node`, `cache_logs`.`uuid` AS `uuid`, `cache_logs`.`cache_id` AS `cache_id`, `caches`.`user_id` AS `cache_owner_id`,
						`caches`.`name` AS `cache_name`, `cache_logs`.`text` AS `log_text`, `cache_logs`.`type` AS `log_type`,
						`cache_logs`.`user_id` AS `log_user_id`, `cache_logs`.`date` AS `log_date`,
						`log_types`.`icon_small` AS `icon_small`,
						`user`.`username` as `log_username`,
						`cache_status`.`allow_user_view`
					 FROM `log_types`, `cache_logs`, `caches`, `user`, `cache_status`
					WHERE `cache_logs`.`id`='&1'
					  AND `cache_logs`.`user_id`=`user`.`user_id`
					  AND `caches`.`cache_id`=`cache_logs`.`cache_id`
					  AND `caches`.`status`=`cache_status`.`id`
					  AND `log_types`.`id`=`cache_logs`.`type`", $log_id, $locale);

			//log exists?
			if (mysql_num_rows($log_rs) == 1)
			{
				$log_record = sql_fetch_array($log_rs);
				mysql_free_result($log_rs);

				include($stylepath . '/removelog.inc.php');

				if ($log_record['node'] != $oc_nodeid)
				{
					tpl_errorMsg('removelog', $error_wrong_node);
					exit;
				}

				if ($log_record['allow_user_view'] != 1 && $log_record['cache_owner_id'] != $usr['userid'])
					exit;

				//cache-owner or log-owner
				if (($log_record['log_user_id'] == $usr['userid']) || ($log_record['cache_owner_id'] == $usr['userid']))
				{
					//Daten lesen
					$commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

					//we are the logger
					if ($log_record['log_user_id'] == $usr['userid'])
					{
						$tplname = 'removelog_logowner';
					}
					else
					{
						$tplname = 'removelog_cacheowner';

						if ($commit == 1)
						{
							//send email to logowner schicken
							$email_content = read_file($stylepath . '/email/removed_log.email');

							$message = isset($_POST['logowner_message']) ? $_POST['logowner_message'] : '';
							if ($message != '')
							{
								//message to logowner
								$message = $removed_message_titel . "\n" . $message . "\n" . $removed_message_end;
							}

							//get cache owner name
							$cache_owner_rs = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $log_record['cache_owner_id']);
							$cache_owner_record = sql_fetch_array($cache_owner_rs);

							//get email address of logowner
							$log_user_rs = sql("SELECT `email`, `username` FROM `user` WHERE `user_id`='&1'", $log_record['log_user_id']);
							$log_user_record = sql_fetch_array($log_user_rs);

							$email_content = mb_ereg_replace('%log_owner%', $log_user_record['username'], $email_content);
							$email_content = mb_ereg_replace('%cache_owner%', $cache_owner_record['username'], $email_content);
							$email_content = mb_ereg_replace('%cache_name%', $log_record['cache_name'], $email_content);
							$email_content = mb_ereg_replace('%comment%', $message, $email_content);

							//send email
							mb_send_mail($log_user_record['email'], $removed_log_title, $email_content, $emailheaders);
						}
					}

					if ($commit == 1)
					{
						// move to archive
						sql("INSERT INTO `cache_logs_archived` SELECT * FROM `cache_logs` WHERE `cache_logs`.`id`='&1' LIMIT 1", $log_id);

						//log entfernen
						sql("DELETE FROM `cache_logs` WHERE `cache_logs`.`id`='&1' LIMIT 1", $log_id);

						// remove cache from users top caches, because the found log was deleted for some reason
						sql("DELETE FROM `cache_rating` WHERE `user_id` = '&1' AND `cache_id` = '&2'", $log_record['log_user_id'], $log_record['cache_id']);

						// do not use slave server for the next time ...
						db_slave_exclude();

						//call eventhandler
						require_once($opt['rootpath'] . 'lib/eventhandler.inc.php');
						event_remove_log($cacheid, $usr['userid']+0);

						//cache anzeigen
						tpl_redirect('viewcache.php?cacheid=' . urlencode($log_record['cache_id']));
						exit;
					}

					// quickfix: this is coded in res_logentry_logitem.tpl (after smarty migration)
					switch ($log_record['log_type'])
					{
						case 1:
							$sLogTypeText = t("%1 found the Geocache", $log_record['log_username']);
							break;
						case 2:
							$sLogTypeText = t("%1 didn't find the Geoacache", $log_record['log_username']);
							break;
						case 3:
							$sLogTypeText = t("%1 wrote a note", $log_record['log_username']);
							break;
						case 7:
							$sLogTypeText = t("%1 has visited the event", $log_record['log_username']);
							break;
						case 8:
							$sLogTypeText = t("%1 wants to visit the event", $log_record['log_username']);
							break;
						default:
							$sLogTypeText = $log_record['log_username'];
							break;
					}

					tpl_set_var('cachename', htmlspecialchars($log_record['cache_name'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', htmlspecialchars($log_record['cache_id'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logid_urlencode', htmlspecialchars(urlencode($log_id), ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logid', htmlspecialchars($log_id, ENT_COMPAT, 'UTF-8'));

					tpl_set_var('logimage', icon_log_type($log_record['icon_small'], ""));
					tpl_set_var('date', htmlspecialchars(strftime($dateformat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'));
					tpl_set_var('userid', htmlspecialchars($log_record['log_user_id'] + 0, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('username', htmlspecialchars($log_record['log_username'], ENT_COMPAT, 'UTF-8'));
					tpl_set_var('typetext', htmlspecialchars($sLogTypeText, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logtext', $log_record['log_text']);
					tpl_set_var('log_user_name', htmlspecialchars($log_record['log_username'], ENT_COMPAT, 'UTF-8'));
				}
				else
				{
					//TODO: hm ... no permission to remove the log
				}
			}
			else
			{
				//TODO: log doesn't exist
			}
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>