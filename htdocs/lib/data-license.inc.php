<?php
/***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		data-lice related functions
	***************************************************************************/

/*
 * userid:        user-id of the copyright holder
 * username:      username of the copyright holder
 * userlicense:   user.date_license of the copyright holder
 * cacheid:       cache to which the licensed content is attached
 * language:      language code for translation of the license disclaimer
 * logdisclaimer: append "all logs entries &copy; their authors"
 *
 * username and userlicense are not queried *here* for performance reasons.
 */

function getLicenseDisclaimer($userid, $username, $userlicense, $cacheid, $language,
                              $logdisclaimer, $html, $twolines=false)
{
	global $opt, $translate, $absolute_server_URI;

	$ltext = "";
	$language = strtoupper($language);

	if ($opt['logic']['license']['disclaimer'])
	{
		if ($userlicense == NEW_DATA_LICENSE_ACTIVELY_ACCEPTED ||
		    $userlicense == NEW_DATA_LICENSE_PASSIVELY_ACCEPTED)
		{
			// © $USERNAME, Opencaching.de, CC BY-NC-ND, as of $DATUM
			$asof = $translate->t('as of', '', '', 0, '', 1, $language);

			if (isset($opt['locale'][$language]['page']['license_url']))
				$lurl = $opt['locale'][$language]['page']['license_url'];
			else
				$lurl = $opt['locale']['EN']['page']['license_url'];
			if (isset($opt['locale'][$language]['format']['phpdate']))
				$df = $opt['locale'][$language]['format']['phpdate'];
			else
				$df = 'd-m-Y';

			$purl = parse_url($absolute_server_URI);
			// may be shortened if linked to www.opencaching.de
			if ($html && strpos($purl['host'],'opencaching.de'))
				$purl['host'] = "Opencaching.de";

			$ltext = "&copy; ";
			if ($html) $ltext .= "<a href='" . $absolute_server_URI . "viewprofile.php?userid=" . $userid . "' target='_blank'>";
			$ltext .= $username;
			if ($html) $ltext .= "</a>";
			$ltext .= ", ";
			if ($html) $ltext .= "<a href='" . $absolute_server_URI . "viewcache.php?cacheid=" . $cacheid . "' target='_blank'>";
			$ltext .= $purl['host'];
			if ($html) $ltext .= "</a>";
			$ltext .= ", ";
			if ($html) $ltext .= "<a href='" . $lurl . "' target='_blank'>";
			$ltext .= "CC BY-NC-ND";
			if ($html) $ltext .= "</a>";
			$ltext .= ", ";
			$ltext .= $asof . " " . date($df);
		}

		if ($logdisclaimer)
		{
			if ($ltext != "")
				if ($twolines) $ltext .= ";\r\n";
				else $ltext .= "; ";
			$ltext .= $translate->t('all log entries &copy; their authors', '', '', 0, '', 1, $language);
		}
	}

	if ($html)
		return $ltext;
	else
		return mb_ereg_replace("&copy;","©",$ltext);
}

?>