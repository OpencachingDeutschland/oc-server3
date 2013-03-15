<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');
require_once($opt['rootpath'] . 'lib2/logic/const.inc.php');

class picture
{
	var $nPictureId = 0;
	var $rePicture;
	var $sFileExtension = '';
	var $bFilenamesSet = false;

	static function pictureIdFromUUID($uuid)
	{
		$pictureid = sql_value("SELECT `id` FROM `pictures` WHERE `uuid`='&1'", 0, $uuid);
		return $pictureid;
	}

	static function fromUUID($uuid)
	{
		$pictureid = picture::pictureIdFromUUID($uuid);
		if ($pictureid == 0)
			return null;

		return new picture($pictureid);
	}

	function __construct($nNewPictureId=ID_NEW)
	{
		global $opt;

		$this->rePicture = new rowEditor('pictures');
		$this->rePicture->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->rePicture->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->rePicture->addInt('node', 0, false);
		$this->rePicture->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->rePicture->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->rePicture->addString('url', '', false);
		$this->rePicture->addString('title', '', false);
		$this->rePicture->addDate('last_url_check', 0, true);
		$this->rePicture->addInt('object_id', null, false);
		$this->rePicture->addInt('object_type', null, false);
		$this->rePicture->addString('thumb_url', '', false);
		$this->rePicture->addDate('thumb_last_generated', 0, false);
		$this->rePicture->addInt('spoiler', 0, false);
		$this->rePicture->addInt('local', 0, false);
		$this->rePicture->addInt('unknown_format', 0, false);
		$this->rePicture->addInt('display', 1, false);
		$this->rePicture->addInt('mappreview', 0, false);

		$this->nPictureId = $nNewPictureId+0;

		if ($nNewPictureId == ID_NEW)
		{
			$this->rePicture->addNew(null);

			$sUUID = mb_strtoupper(sql_value("SELECT UUID()", ''));
			$this->rePicture->setValue('uuid', $sUUID);
			$this->rePicture->setValue('node', $opt['logic']['node']['id']);
		}
		else
		{
			$this->rePicture->load($this->nPictureId);

			$sFilename = $this->getFilename();
			$fna = mb_split('\\.', $sFilename);
			$this->sFileExtension = mb_strtolower($fna[count($fna) - 1]);

			$this->bFilenamesSet = true;
		}
	}

	function exist()
	{
		return $this->rePicture->exist();
	}

	static function allowedExtension($sFilename)
	{
		global $opt;

		if (strpos($sFilename, ';') !== false)
			return false;
		if (strpos($sFilename, '.') === false)
			return false;

		$sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));
		
		if (strpos(';' . $opt['logic']['pictures']['extensions'] . ';', ';' . $sExtension . ';') !== false)
			return true;
		else
			return false;
	}

	function setFilenames($sFilename)
	{
		global $opt;

		if ($this->bFilenamesSet == true)
			return;
		if (strpos($sFilename, '.') === false)
			return;
		$sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));

		$sUUID = $this->getUUID();

		$this->sFileExtension = $sExtension;
		$this->setUrl($opt['logic']['pictures']['url'] . $sUUID . '.' . $sExtension);
		//$this->setThumbUrl($opt['logic']['pictures']['thumb_url'] . substr($sUUID, 0, 1) . '/' . substr($sUUID, 1, 1) . '/' . $sUUID . '.' . $sExtension);
		$this->bFilenamesSet = true;
	}

	function getPictureId()
	{
		return $this->nPictureId;
	}

	function delete()
	{
		global $opt;

		// delete record, image and thumb
		@unlink($this->getFilename());
		@unlink($this->getThumbFilename());

		sql("DELETE FROM `pictures` WHERE `id`='&1'", $this->nPictureId);

		return true;
	}

	function getUrl()
	{
		return $this->rePicture->getValue('url');
	}
	function setUrl($value)
	{
		return $this->rePicture->setValue('url', $value);
	}
	function getThumbUrl()
	{
		return $this->rePicture->getValue('thumb_url');
	}
	function setThumbUrl($value)
	{
		return $this->rePicture->setValue('thumb_url', $value);
	}
	function getTitle()
	{
		return $this->rePicture->getValue('title');
	}
	function setTitle($value)
	{
		if ($value != '')
			return $this->rePicture->setValue('title', $value);
		else
			return false;
	}
	function getSpoiler()
	{
		return $this->rePicture->getValue('spoiler')!=0;
	}
	function setSpoiler($value)
	{
		return $this->rePicture->setValue('spoiler', $value ? 1 : 0);
	}
	function getLocal()
	{
		return $this->rePicture->getValue('local')!=0;
	}
	function setLocal($value)
	{
		return $this->rePicture->setValue('local', $value ? 1 : 0);
	}
	function getDisplay()
	{
		return $this->rePicture->getValue('display')!=0;
	}
	function setDisplay($value)
	{
		return $this->rePicture->setValue('display', $value ? 1 : 0);
	}
	function getMapPreview()
	{
		return $this->rePicture->getValue('mappreview') != 0;
	}
	function setMapPreview($value)
	{
		return $this->rePicture->setValue('mappreview', $value ? 1 : 0);
	}
	function getFilename()
	{
		global $opt;

		if (mb_substr($opt['logic']['pictures']['dir'], -1, 1) != '/')
			$opt['logic']['pictures']['dir'] .= '/';

		$uuid = $this->getUUID();
		$url = $this->getUrl();
		$fna = mb_split('\\.', $url);
		$extension = mb_strtolower($fna[count($fna) - 1]);
		
		return $opt['logic']['pictures']['dir'] . $uuid . '.' . $extension;
	}
	function getThumbFilename()
	{
		global $opt;

		if (mb_substr($opt['logic']['pictures']['thumb_dir'], -1, 1) != '/')
			$opt['logic']['pictures']['thumb_dir'] .= '/';

		$uuid = $this->getUUID();
		$url = $this->getUrl();
		$fna = mb_split('\\.', $url);
		$extension = mb_strtolower($fna[count($fna) - 1]);

		$dir1 = mb_strtoupper(mb_substr($uuid, 0, 1));
		$dir2 = mb_strtoupper(mb_substr($uuid, 1, 1));

		return $opt['logic']['pictures']['thumb_dir'] . $dir1 . '/' . $dir2 . '/' . $uuid . '.' . $extension;
	}
	function getLogId()
	{
		if ($this->getObjectType() == OBJECT_CACHELOG)
			return $this->getObjectId();
		else
			return false;
	}
	function isVisibleOnCachePage()
	{
		if ($this->getObjectType() != OBJECT_CACHELOG)
			return null;
		else
			$rs = sql("SELECT `id` FROM `cache_logs` WHERE `cache_id`='&1'
                         ORDER BY `date`, `id` DESC
                            LIMIT &2",
                $this->getCacheId(), MAX_LOGENTRIES_ON_CACHEPAGE);
		$firstlogs = false;
		while ($r = sql_fetch_assoc($rs))
			if ($r['id'] == $this->getLogId())
				$firstlogs = true;

		sql_free_result($rs);
		return $firstlogs;
	}

	function getCacheId()
	{
		if ($this->getObjectType() == OBJECT_CACHELOG)
			return sql_value("SELECT `cache_id` FROM `cache_logs` WHERE `id`='&1'", false, $this->getObjectId());
		else if ($this->getObjectType() == OBJECT_CACHE)
			return $this->getObjectId();
		else
			return false;
	}
	function getObjectId()
	{
		return $this->rePicture->getValue('object_id');
	}
	function setObjectId($value)
	{
		return $this->rePicture->setValue('object_id', $value+0);
	}
	function getObjectType()
	{
		return $this->rePicture->getValue('object_type');
	}
	function setObjectType($value)
	{
		return $this->rePicture->setValue('object_type', $value+0);
	}
	function getUserId()
	{
		if ($this->getObjectType() == OBJECT_CACHE)
			return sql_value("SELECT `caches`.`user_id` FROM `caches` WHERE `caches`.`cache_id`='&1'", false, $this->getObjectId());
		else if ($this->getObjectType() == OBJECT_CACHELOG)
			return sql_value("SELECT `cache_logs`.`user_id` FROM `cache_logs` WHERE `cache_logs`.`id`='&1'", false, $this->getObjectId());
		else
			return false;
	}

	function getNode()
	{
		return $this->rePicture->getValue('node');
	}
	function setNode($value)
	{
		return $this->rePicture->setValue('node', $value);
	}
	function getUUID()
	{
		return $this->rePicture->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->rePicture->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->rePicture->getValue('date_created');
	}
	function getAnyChanged()
	{
		return $this->rePicture->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		if ($this->bFilenamesSet == false)
			return false;

		$bRetVal = $this->rePicture->save();

		if ($bRetVal)
		{
			$this->nPictureId = $this->rePicture->getValue('id');
			if ($this->getObjectType() == OBJECT_CACHE && $this->getMapPreview())
				sql("UPDATE `pictures` SET `mappreview`=0 WHERE `object_type`='&1' AND `object_id`='&2' AND `id`!='&3'", 
				    OBJECT_CACHE, $this->getObjectId(), $this->getPictureId());
			sql_slave_exclude();
		}

		return $bRetVal;
	}

	function allowEdit()
	{
		global $login;

		$login->verify();

		if (sql_value("SELECT COUNT(*) FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1') AND `caches`.`cache_id`='&2'", 0, $login->userid, $this->getCacheId()) == 0)
			return false;
		else if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
}
?>