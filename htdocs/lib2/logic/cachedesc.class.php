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

class user
{
	var $nCacheDescId = 0;
	var $reCacheDesc;

	function __construct($nNewCacheDescId=ID_NEW)
	{
		$this->reUser = new rowEditor('cache_desc');
		$this->reUser->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reUser->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->reUser->addInt('node', 0, false);
		$this->reUser->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reUser->addInt('cache_id', 0, false);
		$this->reUser->addString('language', '', false);
		$this->reUser->addString('desc', '', false);
		$this->reUser->addInt('desc_htmledit', 1, false);
		$this->reUser->addString('hint', '', false);
		$this->reUser->addString('short_desc', '', false);

		$this->nCacheDescId = $nNewCacheDescId+0;

		if ($nNewCacheDescId == ID_NEW)
		{
			$this->reCacheDesc->addNew(null);
		}
		else
		{
			$this->reCacheDesc->load($this->nCacheDescId);
		}
	}

	function exist()
	{
		return $this->reCacheDesc->exist();
	}

	function getId()
	{
		return $this->reCacheDesc->getValue('id');
	}
	function getUUID()
	{
		return $this->reCacheDesc->getValue('uuid');
	}
	function getNode()
	{
		return $this->reCacheDesc->getValue('node');
	}
	function setNode($value)
	{
		return $this->reCacheDesc->setValue('node', $value);
	}
	function getDateCreated()
	{
		return $this->reCacheDesc->getValue('date_created');
	}
	function getLastModified()
	{
		return $this->reCacheDesc->getValue('last_modified');
	}
	function getCacheId()
	{
		return $this->reCacheDesc->getValue('cache_id');
	}
	function getLanguage()
	{
		return $this->reCacheDesc->getValue('language');
	}
	function getDescAsHtml()
	{
		return $this->reCacheDesc->getValue('desc');
	}
	function getDescHtmlEdit()
	{
		return ($this->reCacheDesc->getValue('desc_htmledit')!=0);
	}
	function getHint()
	{
		return $this->reCacheDesc->getValue('hint');
	}
	function getShortDesc()
	{
		return $this->reCacheDesc->getValue('short_desc');
	}

	function getAnyChanged()
	{
		return $this->reCacheDesc->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		sql_slave_exclude();
		return $this->reCacheDesc->save();
	}

	function reload()
	{
		$this->reCacheDesc->reload();
	}
}
?>