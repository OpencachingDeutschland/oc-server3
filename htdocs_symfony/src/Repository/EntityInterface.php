<?php

namespace Oc\Repository;

interface EntityInterface
{
    /**
     * Checks if the entity is new.
     */
    public function isNew(): bool;

    /**
     * Sets all properties from array.
     */
    public function fromArray(array $data);

    /**
     * Returns all properties as array.
     */
    public function toArray(): array;
}
