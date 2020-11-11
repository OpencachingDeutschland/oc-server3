<?php

class GkItemWaypointEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $wp;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
