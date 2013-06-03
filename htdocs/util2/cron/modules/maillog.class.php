<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Process system maillog to detect email delivery problems
 ***************************************************************************/

checkJob(new maillog());


class maillog
{
	var $name = 'maillog';
	var $interval = 600;  // every 10 minutes


	function run()
	{
		global $opt;
		if ($opt['system']['maillog']['syslog_db_host'] != '')
			if ($opt['system']['maillog']['syslog_mta'] != 'postfix/smtp')
			{
				echo $this->name.": unknown MTA '".$opt['system']['maillog']['syslog_mta']."'\n";
				return;
			}
			else
				$this->process_syslog();
	}


	function process_syslog()
	{
		global $opt;

		$dbc = @mysql_connect($opt['system']['maillog']['syslog_db_host'],
		                     $opt['system']['maillog']['syslog_db_user'],
		                     $opt['system']['maillog']['syslog_db_password']);
		if ($dbc === FALSE)
		{
			echo $this->name.": could not connect to syslog database\n";
			return;
		}
		if (@mysql_query("USE ".$opt['system']['maillog']['syslog_db_name']) === FALSE)
		{
			echo $this->name.": could not open syslog database: ".mysql_error()."\n";
			return;
		}

		$last_id = sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='syslog_maillog_lastid'", 0);
		$last_date = sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='syslog_maillog_lastdate'", "");

		// We check for both, new IDs and new creation dates, so that it still works
		// if the syslog DB is re-setup and IDs restarted from 1 (dates are not unique).
		$rs = @mysql_query(
			  "SELECT `id`, `message`, `created`
			     FROM `event`
			    WHERE  (`id`>'" . mysql_real_escape_string($last_id) . "' OR `created`>'" . mysql_real_escape_string($last_date) . "')  
			      AND `host_name`='" . mysql_real_escape_string($opt['system']['maillog']['syslog_oc_host']) . "'
			      AND `program`='" . mysql_real_escape_string($opt['system']['maillog']['syslog_mta']) . "'
			 ORDER BY `id`");
		if ($rs === FALSE)
		{
			echo $this->name.": syslog query error (".mysql_errno()."): ".mysql_error()."\n";
			return;
		}

		while ($logentry = mysql_fetch_assoc($rs))
		{
			$message = $logentry['message'];   // latin-1 charset
			$delivered = strpos($message, 'status=sent') > 0;
			$bounced = strpos($message, 'status=bounced') > 0;
			if ($delivered || $bounced)
			{
				if (preg_match('/ to=<(.+)>,/U',$message,$matches))
				{
					$emailadr = $matches[1];
					if ($delivered)
						sql("UPDATE `user` SET `email_problems`=0
						      WHERE `email`='" . mysql_real_escape_string($emailadr) . "'");
					else if ($bounced)
						// maximum one bounce per day is counted, to filter out temporary problems
						sql("UPDATE `user` SET `email_problems`=`email_problems`+1, `last_email_problem`='" . mysql_real_escape_string($logentry['created']) . "'
						      WHERE `email`='" . mysql_real_escape_string($emailadr) . "'
									  AND IFNULL(`last_email_problem`,'') < '" . mysql_real_escape_string(substr($logentry['created'],0,10)) . "'");
			  }
			  else
					echo $this->name.": no email address found for record ID ".$logentry['id']."\n";
			}
			$last_id = $logentry['id'];
			$last_date = $logentry['created'];
		}
		mysql_free_result($rs);

		sql("INSERT INTO `sysconfig` (`name`, `value`) VALUES ('syslog_maillog_lastid','&1')
		     ON DUPLICATE KEY UPDATE `value`='&1'",
		     $last_id);
		sql("INSERT INTO `sysconfig` (`name`, `value`) VALUES ('syslog_maillog_lastdate','&1')
		     ON DUPLICATE KEY UPDATE `value`='&1'",
		     $last_date);
	}
}

?>
