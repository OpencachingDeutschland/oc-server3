<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 * Class NodesEntity
 *
 * @package Oc\Entity
 */
class NodesEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $url;

    /** @var string */
    public $waypointPrefix;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
