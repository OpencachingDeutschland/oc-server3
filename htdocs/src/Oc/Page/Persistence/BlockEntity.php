<?php

namespace Oc\Page\Persistence;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class BlockEntity
 */
class BlockEntity extends AbstractEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $pageId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $html;

    /**
     * @var int
     */
    public $position;

    /**
     * @var DateTime
     */
    public $updatedAt;

    /**
     * @var bool
     */
    public $active;

    /**
     * Checks if the entity is new.
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }
}
