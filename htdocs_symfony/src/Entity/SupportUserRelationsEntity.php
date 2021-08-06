<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 * Class SupportUserRelationsEntity
 *
 * @package Oc\Entity
 */
class SupportUserRelationsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $ocUserId;

    /** @var int */
    public $nodeId;

    /** @var string */
    public $nodeUserId;

    /** @var string */
    public $nodeUsername;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
