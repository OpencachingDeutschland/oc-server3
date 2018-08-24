<?php

class SearchDoublesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $hash;

    /** @var string */
    public $word;

    /** @var string */
    public $simple;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->hash === null;
    }
}
