<?php

class GeoCachesAttributesModifiedEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var int */
    public $attribId;

    /** @var DateTime */
    public $dateModified;

    /** @var int */
    public $wasSet;

    /** @var int */
    public $restoredBy;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
