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
    public int $id;

    public int $ocUserId;

    public int $nodeId;

    public string $nodeUserId;

    public string $nodeUsername;

    public UserEntity $user;

    public NodesEntity $node;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
