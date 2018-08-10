<?php 

class GeoCacheStatusModifiedEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $dateModified;

    /** @var int */
    public $oldState;

    /** @var int */
    public $newState;

    /** @var int */
    public $userId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
