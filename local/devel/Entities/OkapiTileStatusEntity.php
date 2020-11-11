<?php

class OkapiTileStatusEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $z;

    /** @var int */
    public $x;

    /** @var int */
    public $y;

    /** @var int */
    public $status;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->z === null;
    }
}
