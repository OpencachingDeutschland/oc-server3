<?php

namespace Oc\Language;

use Oc\Repository\AbstractEntity;

/**
 * Class LanguageEntity
 */
class LanguageEntity extends AbstractEntity
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
    public $nativeName;

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
     * @var bool
     */
    public $isTranslated;

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
