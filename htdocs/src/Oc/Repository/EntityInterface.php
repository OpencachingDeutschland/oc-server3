<?php

namespace Oc\Repository;

/**
 * Class EntityInterface
 *
 * @package Oc\Repository
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
     *
     * @return void
     */
    public function fromArray(array $data);

    /**
     * Returns all properties as array.
     *
     * @return array
     */
    public function toArray();
}
