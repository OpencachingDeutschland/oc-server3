<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

class cachedesc
{
    public $nCacheDescId = 0;
    public $reCacheDesc;

    public function __construct($nNewCacheDescId = ID_NEW)
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
        $this->reUser->addInt('desc_html', 1, false);
        $this->reUser->addInt('desc_htmledit', 1, false);
        $this->reUser->addString('hint', '', false);
        $this->reUser->addString('short_desc', '', false);

        $this->nCacheDescId = $nNewCacheDescId + 0;

        if ($nNewCacheDescId == ID_NEW) {
            $this->reCacheDesc->addNew(null);
        } else {
            $this->reCacheDesc->load($this->nCacheDescId);
        }
    }

    public function exist()
    {
        return $this->reCacheDesc->exist();
    }

    public function getId()
    {
        return $this->reCacheDesc->getValue('id');
    }

    public function getUUID()
    {
        return $this->reCacheDesc->getValue('uuid');
    }

    public function getNode()
    {
        return $this->reCacheDesc->getValue('node');
    }

    public function setNode($value)
    {
        return $this->reCacheDesc->setValue('node', $value);
    }

    public function getDateCreated()
    {
        return $this->reCacheDesc->getValue('date_created');
    }

    public function getLastModified()
    {
        return $this->reCacheDesc->getValue('last_modified');
    }

    public function getCacheId()
    {
        return $this->reCacheDesc->getValue('cache_id');
    }

    public function getLanguage()
    {
        return $this->reCacheDesc->getValue('language');
    }

    public function getDescAsHtml()
    {
        return $this->reCacheDesc->getValue('desc');
    }

    public function getIsDescHtml()
    {
        return ($this->reCacheDesc->getValue('desc_html') != 0);
    }

    public function getDescHtmlEdit()
    {
        return ($this->reCacheDesc->getValue('desc_htmledit') != 0);
    }

    public function getHint()
    {
        return $this->reCacheDesc->getValue('hint');
    }

    public function getShortDesc()
    {
        return $this->reCacheDesc->getValue('short_desc');
    }

    public function getAnyChanged()
    {
        return $this->reCacheDesc->getAnyChanged();
    }

    // return if successfull (with insert)
    public function save()
    {
        sql_slave_exclude();

        return $this->reCacheDesc->save();
    }

    public function reload()
    {
        $this->reCacheDesc->reload();
    }
}
