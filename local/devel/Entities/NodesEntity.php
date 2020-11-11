<?php

class NodesEntity extends Oc\Repository\AbstractEntity
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
    {
        return $this->id === null;
    }
}
