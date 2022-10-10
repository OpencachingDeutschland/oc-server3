<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class CountriesEntity extends AbstractEntity
{
    public string $short;

    public string $name;

    public int $transId;

    public string $de;

    public string $en;

    public int $listDefaultDe;

    public string $sortDe;

    public int $listDefaultEn;

    public string $sortEn;

    public int $admDisplay2;

    public int $admDisplay3;

    public function isNew(): bool
    {
        return $this->short === null;
    }
}
