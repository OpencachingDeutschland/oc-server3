<?php

namespace Oc\Page\Persistence;

use DateTime;
use Oc\Repository\AbstractEntity;

class PageEntity extends AbstractEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $metaKeywords;

    /**
     * @var string
     */
    public $metaDescription;

    /**
     * @var string
     */
    public $metaSocial;

    /**
     * @var DateTime
     */
    public $updatedAt;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * Checks if the entity is new.
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }
}
