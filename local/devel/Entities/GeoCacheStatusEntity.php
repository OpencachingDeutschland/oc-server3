<?php

class GeoCacheStatusEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /** @var int */
    public $allowUserView;

    /** @var int */
    public $allowOwnerEditStatus;

    /** @var int */
    public $allowUserLog;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
