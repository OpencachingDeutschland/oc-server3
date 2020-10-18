<?php

namespace Oc\Repository;

abstract class AbstractEntity
{
    /**
     * Checks if the entity is new.
     */
    abstract public function isNew(): bool;

    /**
     * Sets all properties from array.
     */
    public function fromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /**
     * Returns all properties as array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
