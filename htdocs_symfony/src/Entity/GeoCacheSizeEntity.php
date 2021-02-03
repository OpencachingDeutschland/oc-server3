<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheSizeEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var int */
    public $ordinal;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
