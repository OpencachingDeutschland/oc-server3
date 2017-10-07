<?php

namespace Oc\Country;

use Oc\Repository\AbstractEntity;

/**
 * Class CountryEntity
 *
 * @package Oc\Country
 */
class CountryEntity extends AbstractEntity
{
    /**
     * @var string
     */
    public $short;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $de;

    /**
     * @var string
     */
    public $en;

    /**
     * @var int
     */
    public $translationId;

    /**
     * @var bool
     */
    public $listDefaultDe;

    /**
     * @var bool
     */
    public $listDefaultEn;

    /**
     * @var string
     */
    public $sortDe;

    /**
     * @var string
     */
    public $sortEn;

    /**
     * Checks if the entity is new.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->short === null;
    }
}
