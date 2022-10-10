<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheStatusEntity extends AbstractEntity
{
    public int $id;

    public string $name;

    public int $transId;

    public string $de;

    public string $en;

    public int $allowUserView;

    public int $allowOwnerEditStatus;

    public int $allowUserLog;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
