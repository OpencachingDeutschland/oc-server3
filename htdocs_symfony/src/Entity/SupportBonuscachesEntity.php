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
    /** @var int */
    public $id;

    /** @var string */
    public $wpOc;

    /** @var bool */
    public $isBonusCache;

    /** @var string */
    public $belongsToBonusCache;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
