<?php

namespace Oc\Language;

use Oc\Repository\AbstractEntity;

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
     */
    public function isNew(): bool
    {
        return $this->short === null;
    }
}
