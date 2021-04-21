<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheReportStatusEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
