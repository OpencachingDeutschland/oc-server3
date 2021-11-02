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
     * @param string $wpOc
     * @param bool $isBonusCache
     * @param string $belongsToBonusCache
     */
    public function __construct(string $wpOc = '', bool $isBonusCache = false, string $belongsToBonusCache = '')
    {
        $this->wpOc = $wpOc;
        $this->isBonusCache = $isBonusCache;
        $this->belongsToBonusCache = $belongsToBonusCache;
    }

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
