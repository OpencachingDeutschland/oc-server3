<?php

class RatingTopsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var int */
    public $rating;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
