<?php

namespace Oc\Repository;

/**
 * Class EntityInterface
 */
interface EntityInterface
{
    /**
     * Checks if the entity is new.
     *
     * @return bool
     */
    public function isNew();

    /**
     * Sets all properties from array.
     *
     * @param array $data
     */
    public function fromArray(array $data);

    /**
     * Returns all properties as array.
     *
     * @return array
     */
    public function toArray();
}
