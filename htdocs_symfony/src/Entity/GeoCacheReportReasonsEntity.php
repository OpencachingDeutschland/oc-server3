<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheReportReasonsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var int */
    public $order;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
