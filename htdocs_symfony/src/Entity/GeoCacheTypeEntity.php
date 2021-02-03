<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheTypeEntity extends AbstractEntity
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
    public $short;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /** @var string */
    public $iconLarge;

    /** @var string */
    public $short2;

    /** @var int */
    public $short2TransId;

    /** @var string */
    public $kmlName;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
