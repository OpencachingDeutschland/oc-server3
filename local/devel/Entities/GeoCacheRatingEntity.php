<?php

class GeoCacheRatingEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var int */
    public $userId;

    /** @var DateTime */
    public $ratingDate;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
