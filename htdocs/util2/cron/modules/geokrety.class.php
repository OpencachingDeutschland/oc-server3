<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Import new data from geokrety.org.
 *
 *  See util2/geokrety for check and repair functions.
 *  See discussion in http://redmine.opencaching.de/issues/18.
 ***************************************************************************/

checkJob(new geokrety());


class geokrety
{
	var $name = 'geokrety';
	var $interval = 900;

	function run()
	{
		global $opt;

		if ($opt['cron']['geokrety']['run'])
		{
			$xmlfile = $this->loadXML();
			if ($xmlfile == false) return;

			$this->importXML($xmlfile);
			if (!$opt['cron']['geokrety']['xml_archive'])
				$this->removeXML($xmlfile);
		}
	}

	/* get file from XML interface 
	 * and return path of saved xml
	 * or false on error
	 */
	function loadXML()
	{
		global $opt;
		
		@mkdir($opt['rootpath'] . 'cache2/geokrety');
		$path = $opt['rootpath'] . 'cache2/geokrety/import-' . date('Ymd-His') . '.xml';

		// Changed default-value for getSysConfig() from '2005-01-01 00:00:00' to 'NOW - 9d 12h'
		// to safely stay in api-limit, even when client and server are in different time zones.
		$modifiedsince = strtotime(getSysConfig('geokrety_lastupdate', date($opt['db']['dateformat'], time() - 60*60*24*9.5)));
		if (!@copy('http://geokrety.org/export.php?modifiedsince=' . date('YmdHis', $modifiedsince - 1), $path))
			return false;

		return $path;
	}

	/* remove given file
	 */
	function removeXML($file)
	{
		@unlink($file);
	}

	/* import the given XML file
	 */
	function importXML($file)
	{
		global $opt;

    $xr = new XMLReader();
    if (!$xr->open($file))
    {
      $xr->close();
			return;
    }

    $xr->read();
    if ($xr->nodeType != XMLReader::ELEMENT)
    {
      echo 'error: First element expected, aborted' . "\n";
      return;
    }
    if ($xr->name != 'gkxml')
    {
      echo 'error: First element not valid, aborted' . "\n";
      return;
    }

		$startupdate = $xr->getAttribute('date');
    if ($startupdate == '')
    {
      echo 'error: Date attribute not valid, aborted' . "\n";
      return;
    }

    while ($xr->read() && !($xr->name == 'geokret' || $xr->name == 'moves')) ;

    $nRecordsCount = 0;
    do
    {
      if ($xr->nodeType == XMLReader::ELEMENT)
      {
				$element = $xr->expand();
				switch ($xr->name)
				{
					case 'geokret':
						$this->importGeoKret($element);
						break;
					case 'moves':
						$this->importMove($element);
						break;
				}

				$nRecordsCount++;
      }
    }
    while ($xr->next());

    $xr->close();

		setSysConfig('geokrety_lastupdate', date($opt['db']['dateformat'], strtotime($startupdate)));
	}

	function importGeoKret($element)
	{
		global $opt;

		$id = $element->getAttribute('id');

		$name = html_entity_decode($this->GetNodeValue($element, 'name'));
		if ($name == '') return;

		$userid = $this->GetNodeAttribute($element, 'owner', 'id')+0;
		$username = $this->GetNodeValue($element, 'owner');
		$this->checkUser($userid, $username);

		$typeid = $this->GetNodeAttribute($element, 'type', 'id')+0;
		$typename = $this->GetNodeValue($element, 'type');
		$this->checkGeoKretType($typeid, $typename);
				
		$description = html_entity_decode($this->GetNodeValue($element, 'description'));
		$datecreated = strtotime($this->GetNodeValue($element, 'datecreated'));
		
		$distancetravelled = $this->GetNodeValue($element, 'distancetravelled')+0;
		$state = $this->GetNodeValue($element, 'state')+0;
		
		$longitude = $this->GetNodeAttribute($element, 'position', 'longitude')+0;
		$latitude = $this->GetNodeAttribute($element, 'position', 'latitude')+0;

		sql("INSERT INTO `gk_item`
					(`id`, `name`, `description`, `userid`, `datecreated`, `distancetravelled`, `latitude`, `longitude`, `typeid`, `stateid`)
				VALUES
					('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9', '&10')
			ON DUPLICATE KEY UPDATE
					`name`='&2', `description`='&3', `userid`='&4', `datecreated`='&5', `distancetravelled`='&6', `latitude`='&7', `longitude`='&8', `typeid`='&9', `stateid`='&10'",
			$id, $name, $description, $userid, date($opt['db']['dateformat'], $datecreated), $distancetravelled, $latitude, $longitude, $typeid, $state);

		// Deleting and inserting item-waypoints if they have not changed will
		// update caches.meta_last_modified -> caches.okapi_syncbase and thus trigger
		// OKAPI changelog actions. This probably can be ignored as OKAPI will verify
		// if data has really changed.

		// update associated waypoints
		/**
		 * This does not work properly, because geokret.waypoints does NOT contain the
		 * current location of the Kret but something like the last cache where it was logged.
		 * Evaluating the 'state' fielt might help, but for now, we import waypoint data
		 * from the moves instead.

		sql("DELETE FROM `gk_item_waypoint` WHERE id='&1'", $id);
		$waypoints = $element->getElementsByTagName('waypoints');
		if ($waypoints->length > 0)
		{
			$wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
			for ($i = 0; $i < $wpItems->length; $i++)
			{
				$wp = $wpItems->item($i)->nodeValue;
				if ($wp != '')
					sql("INSERT INTO `gk_item_waypoint`
								(`id`, `wp`)
							VALUES
								('&1', '&2')",
					$id, $wp);
			}
		}
		*/
	}


	function importMove($element)
	{
		global $opt;

		$id = $element->getAttribute('id')+0;

		$gkid = $this->GetNodeAttribute($element, 'geokret', 'id')+0;
		if (sql_value("SELECT COUNT(*) FROM `gk_item` WHERE `id`='&1'", 0, $gkid) == 0) return;

		$latitude = $this->GetNodeAttribute($element, 'position', 'latitude')+0;
		$longitude = $this->GetNodeAttribute($element, 'position', 'longitude')+0;

		$datelogged = strtotime($this->GetNodeAttribute($element, 'date', 'logged'));
		$datemoved = strtotime($this->GetNodeAttribute($element, 'date', 'moved'));
		$userid = $this->GetNodeAttribute($element, 'user', 'id')+0;
		$username = $this->GetNodeValue($element, 'user');
		$this->checkUser($userid, $username);

		$comment = html_entity_decode($this->GetNodeValue($element, 'comment'));
		$logtypeid = $this->GetNodeAttribute($element, 'logtype', 'id')+0;
		$logtypename = $this->GetNodeValue($element, 'logtype');
		$this->checkMoveType($logtypeid, $logtypename);

		sql("INSERT INTO `gk_move`
					(`id`, `itemid`, `latitude`, `longitude`, `datemoved`, `datelogged`, `userid`, `comment`, `logtypeid`)
				VALUES
					('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9')
			ON DUPLICATE KEY UPDATE
					`itemid`='&2', `latitude`='&3', `longitude`='&4', `datemoved`='&5', `datelogged`='&6', `userid`='&7', `comment`='&8', `logtypeid`='&9'",
		$id, $gkid, $latitude, $longitude, date($opt['db']['dateformat'], $datemoved), date($opt['db']['dateformat'], $datelogged), $userid, $comment, $logtypeid);
		
		sql("DELETE FROM `gk_move_waypoint` WHERE id='&1'", $id);

		// update associated waypoints
		$waypoints = $element->getElementsByTagName('waypoints');
		if ($waypoints->length > 0)
		{
			$wpItems = $waypoints->item(0)->getElementsByTagName('waypoint');
			for ($i = 0; $i < $wpItems->length; $i++)
			{
				$wp = mb_trim($wpItems->item($i)->nodeValue);
				if ($wp != '')
					sql("INSERT INTO `gk_move_waypoint` (`id`, `wp`) VALUES ('&1', '&2')", $id, $wp);
			}
		}

		// update the current gk-waypoints based on the last move
		sql("DELETE FROM `gk_item_waypoint` WHERE `id`='&1'", $gkid);
		$rs = sql("
				SELECT `id`,`logtypeid` FROM `gk_move`
				WHERE `itemid`='&1' AND `logtypeid`!=2
				ORDER BY `datemoved` DESC LIMIT 1",
			$gkid);
		$r = sql_fetch_assoc($rs);
		sql_free_result($rs);
		if ($r === false) return;

		if ($r['logtypeid'] == 0 /* dropped */ || $r['logtypeid'] == 3 /* seen in */)
		{
			sql("
				INSERT INTO `gk_item_waypoint` (`id`, `wp`)
				SELECT '&1' AS `id`, `wp`
				FROM `gk_move_waypoint`
				WHERE `id`='&2' AND `wp`!=''",
				$gkid, $r['id']);  // "late log" bugfix: replaced $id paramter by $r['id']
		}
		else
		{
			// do nothing
		}
	}


	function checkGeoKretType($id, $name)
	{
		sql("INSERT INTO `gk_item_type` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", $id, $name);
	}


	function checkUser($id, $name)
	{
		if ($id == 0)	return;

		sql("INSERT INTO `gk_user` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", $id, $name);
	}


	function checkMoveType($id, $name)
	{
		sql("INSERT INTO `gk_move_type` (`id`, `name`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `name`='&2'", $id, $name);
	}


	function GetNodeValue(&$domnode, $element)
	{
		$subnode = $domnode->getElementsByTagName($element);
		if ($subnode->length < 1)
			return '';
		else
			return $subnode->item(0)->nodeValue;
	}


	function GetNodeAttribute(&$domnode, $element, $attr)
	{
		$subnode = $domnode->getElementsByTagName($element);
		if ($subnode->length < 1)
			return '';
		else
			return $subnode->item(0)->getAttribute($attr);
	}
}
?>