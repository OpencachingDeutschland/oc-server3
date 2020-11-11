<?php

class OkapiTileCachesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $z;

    /** @var int */
    public $x;

    /** @var int */
    public $y;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $z21x;

    /** @var int */
    public $z21y;

    /** @var int */
    public $status;

    /** @var int */
    public $type;

    /** @var int */
    public $rating;

    /** @var int */
    public $flags;

    /** @var int */
    public $nameCrc;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->z === null;
    }
}
