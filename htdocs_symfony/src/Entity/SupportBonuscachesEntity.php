<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 * Class SupportBonuscachesEntity
 *
 * @package Oc\Entity
 */
class SupportBonuscachesEntity extends AbstractEntity
{
    public int $id;

    public string $wpOc;

    public bool $isBonusCache;

    public string $belongsToBonusCache;

    public function __construct(string $wpOc = '', bool $isBonusCache = false, string $belongsToBonusCache = '')
    {
        $this->wpOc = $wpOc;
        $this->isBonusCache = $isBonusCache;
        $this->belongsToBonusCache = $belongsToBonusCache;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
