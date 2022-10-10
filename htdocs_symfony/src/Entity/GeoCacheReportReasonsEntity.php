<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheReportReasonsEntity extends AbstractEntity
{
    public int $id;

    public string $name;

    public int $transId;

    public int $order;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
