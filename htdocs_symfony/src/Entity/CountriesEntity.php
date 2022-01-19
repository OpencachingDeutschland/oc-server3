<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 *
 */
class CountriesEntity extends AbstractEntity
{
    /** @var string */
    public $short;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /** @var int */
    public $listDefaultDe;

    /** @var string */
    public $sortDe;

    /** @var int */
    public $listDefaultEn;

    /** @var string */
    public $sortEn;

    /** @var int */
    public $admDisplay2;

    /** @var int */
    public $admDisplay3;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->short === null;
    }
}
