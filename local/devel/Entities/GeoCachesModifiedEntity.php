<?php

class GeoCachesModifiedEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $dateModified;

    /** @var string */
    public $name;

    /** @var int */
    public $type;

    /** @var DateTime */
    public $dateHidden;

    /** @var int */
    public $size;

    /** @var int */
    public $difficulty;

    /** @var int */
    public $terrain;

    /** @var float */
    public $searchTime;

    /** @var float */
    public $wayLength;

    /** @var string */
    public $wpGc;

    /** @var string */
    public $wpNc;

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
