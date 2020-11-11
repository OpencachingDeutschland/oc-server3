<?php

class SearchIgnoreEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $word;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->word === null;
    }
}
