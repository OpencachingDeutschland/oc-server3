<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheTypeEntity extends AbstractEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

    public int $ordinal;

    public string $short;

    public string $de;

    public string $en;

    public string $iconLarge;

    public string $short2;

    public int $short2TransId;

    public string $kmlName;

    public string $svgName;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
