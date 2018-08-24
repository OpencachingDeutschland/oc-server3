<?php

class PwDictEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $pw;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->pw === null;
    }
}
