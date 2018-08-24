<?php

namespace Oc\Repository;

abstract class AbstractEntity
{
    /**
     * Checks if the entity is new.
     *
     * @return bool
     */
    abstract public function isNew();

    /**
     * Sets all properties from array.
     *
     * @param array $data
     */
    public function fromArray(array $data)
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
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
