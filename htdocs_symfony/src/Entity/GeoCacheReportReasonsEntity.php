<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheReportReasonsEntity extends AbstractEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

    public int $order;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
