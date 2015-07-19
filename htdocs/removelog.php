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
	require_once($rootpath . 'lib2/html2text.class.php');
	require_once('./lib/recommendation.inc.php');

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
			tpl_set_var('message_start', '');
			tpl_set_var('message_end', '');
			tpl_set_var('helplink', helppagelink('login'));
		}
		else
		{
			$log_rs = sql("SELECT	`cache_logs`.`node` AS `node`, `cache_logs`.`uuid` AS `uuid`, `cache_logs`.`cache_id` AS `cache_id`, `caches`.`user_id` AS `cache_owner_id`,
						`caches`.`name` AS `cache_name`, `cache_logs`.`text` AS `log_text`, `cache_logs`.`text_html`, `cache_logs`.`type` AS `log_type`, `cache_logs`.`oc_team_comment` AS `oc_team_comment`,
						`cache_logs`.`user_id` AS `log_user_id`, `cache_logs`.`date` AS `log_date`,
						`log_types`.`icon_small` AS `icon_small`,
						`user`.`username` as `log_username`,
						`caches`.`wp_oc`,
						`cache_status`.`allow_user_view`,
						IFNULL(`sys_trans_text`.`text`,`log_types`.`en`) AS `logtype_name`
					 FROM `cache_logs`, `caches`, `user`, `cache_status`, `log_types`
			LEFT JOIN `sys_trans_text` ON `sys_trans_text`.`trans_id`=`log_types`.`trans_id` AND `sys_trans_text`.`lang`='&2'
					WHERE `cache_logs`.`id`='&1'
					  AND `cache_logs`.`user_id`=`user`.`user_id`
					  AND `caches`.`cache_id`=`cache_logs`.`cache_id`
					  AND `caches`.`status`=`cache_status`.`id`
					  AND `log_types`.`id`=`cache_logs`.`type`
						", $log_id, $opt['template']['locale']);

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

				if ($log_record['allow_user_view'] != 1 &&
				    $log_record['cache_owner_id'] != $usr['userid'] &&
				    !($usr['admin'] && ADMIN_USER))
					exit;

				// deleted allowed by cache-owner or log-owner
				if (($log_record['log_user_id'] == $usr['userid']) || ($log_record['cache_owner_id'] == $usr['userid']))
				{
					$commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

					$ownlog = ($log_record['log_user_id'] == $usr['userid']);
					if ($ownlog)
					{
						// we are the log-owner
						$tplname = 'removelog_logowner';
					}
					else
					{
						// we are the cache-owner
						$tplname = 'removelog_cacheowner';

						if ($commit == 1)
						{
							//send email to logowner
							$email_content = read_file($stylepath . '/email/removed_log.email');

							$message = isset($_POST['logowner_message']) ? $_POST['logowner_message'] : '';
							if ($message != '')
							{
								//message to logowner
								$message = $removed_message_title . "\n" . $message . "\n" . $removed_message_end;
							}

							$logtext = $log_record['log_text'];
							if ($log_record['text_html'] != 0)
							{
								$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
								$h2t = new html2text($logtext);
								$h2t->set_base_url($absolute_server_URI);
								$logtext = $h2t->get_text();
							}
							$logtext = $removed_text_title . "\n" . trim($logtext) . "\n" . $removed_text_end;

							//get cache owner name
							$cache_owner_rs = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $log_record['cache_owner_id']);
							$cache_owner_record = sql_fetch_array($cache_owner_rs);
							mysql_free_result($cache_owner_rs);

							//get email address of logowner
							$log_user_rs = sql("SELECT `email`, `username` FROM `user` WHERE `user_id`='&1'", $log_record['log_user_id']);
							$log_user_record = sql_fetch_array($log_user_rs);
							mysql_free_result($log_user_rs);

							// insert log data
							$email_content = mb_ereg_replace('%log_owner%', $log_user_record['username'], $email_content);
							$email_content = mb_ereg_replace('%cache_owner%', $cache_owner_record['username'], $email_content);
							$email_content = mb_ereg_replace('%cache_name%', $log_record['cache_name'], $email_content);
							$email_content = mb_ereg_replace('%cache_wp%', $log_record['wp_oc'], $email_content);
							$email_content = mb_ereg_replace('%log_date%', date($opt['locale'][$locale]['format']['phpdate'], strtotime($log_record['log_date'])), $email_content);
							$email_content = mb_ereg_replace('%log_type%', $log_record['logtype_name'], $email_content);
							$email_content = mb_ereg_replace('%log_text%', $logtext, $email_content);
							$email_content = mb_ereg_replace('%comment%', $message, $email_content);

							//send email
							mb_send_mail($log_user_record['email'], $removed_log_title, $email_content, $emailheaders);
						}
					}

					if ($commit == 1)
					{
						// remove log pictures
						// see also picture.class.php: delete()

						$rs = sql("SELECT `id`, `url` FROM `pictures`
						           WHERE `object_type`=1 AND `object_id`='&1'",
                      $log_id);

						while ($r = sql_fetch_assoc($rs))
						{
							if (!$ownlog)
								sql("SET @archive_picop=TRUE");
							else
								sql("SET @archive_picop=FALSE");

							sql("DELETE FROM `pictures` WHERE `id`='&1'", $r['id']);
							$archived = (sqlValue("SELECT `id` FROM `pictures_modified` WHERE `id`=" . $r['id'], 0) > 0);
							$fna = mb_split('\\/', $r['url']);
							$filename = end($fna);
							if (mb_substr($picdir, -1, 1) != '/')
								$path = $picdir . "/";
							else
								$path = $picdir;

							if ($archived)
								@rename($path . $filename, $path . "deleted/" . $filename);
							else
								@unlink($path . $filename);

							$path .= mb_strtoupper(mb_substr($filename, 0, 1)) . '/' .
							         mb_strtoupper(mb_substr($filename, 1, 1)) . '/';
							@unlink($path . $filename);  // Thumb

							/* lib2 code would be ...
							$rs = sql("SELECT `id` FROM `pictures` WHERE `object_type`=1 AND `object_id`='&1'", $log_id);
							while ($r = sql_fetch_assoc($rs))
							{
								$pic = new picture($rs['id']);
								$pic->delete();
							}
							sql_free_result($rs);
							*/
						}
						sql_free_result($rs);

						// evtl. discard cache recommendation
						discard_recommendation($log_id);

						// move to archive, even if own log (uuids are used for OKAPI replication)
						sql("INSERT IGNORE INTO `cache_logs_archived` SELECT *, '0' AS `deletion_date`, '&2' AS `deleted_by`, 0 AS `restored_by` FROM `cache_logs` WHERE `cache_logs`.`id`='&1' LIMIT 1", $log_id, $usr['userid']);

						// remove log entry
						sql("DELETE FROM `cache_logs` WHERE `cache_logs`.`id`='&1' LIMIT 1", $log_id);

						// now tell OKAPI about the deletion;
						// this will trigger an okapi_syncbase update, if OKAPI is installed:
						sql("UPDATE `cache_logs_archived` SET `deletion_date`=NOW() WHERE `id`='&1'", $log_id);

						// do not use slave server for the next time ...
						db_slave_exclude();

						//call eventhandler
						require_once($opt['rootpath'] . 'lib/eventhandler.inc.php');
						event_remove_log($log_record['cache_id'], $log_record['log_user_id']);

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

					if ($log_record['oc_team_comment'])
						$teamimg = '<img src="resource2/ocstyle/images/oclogo/oc-team-comment.png" title="' . t('OC team comment') . '" /> ';
					else
						$teamimg = "";
					tpl_set_var('logimage', $teamimg . icon_log_type($log_record['icon_small'], ""));

					tpl_set_var('date', htmlspecialchars(strftime($dateformat, strtotime($log_record['log_date'])), ENT_COMPAT, 'UTF-8'));
					tpl_set_var('time', substr($log_record['log_date'],11)=="00:00:00" ? "" : ", " . substr($log_record['log_date'],11,5)); 
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