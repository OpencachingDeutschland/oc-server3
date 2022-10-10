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
    public int $id;

    public string $name;

    public string $url;

    public string $waypointPrefix;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
