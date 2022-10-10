<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheReportStatusEntity extends AbstractEntity
{
    public int $id;

    public string $name;

    public int $transId;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
