<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheSizeEntity extends AbstractEntity
{
    public int $id;

    public string $name;

    public int $transId;

    public int $ordinal;

    public string $de;

    public string $en;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
