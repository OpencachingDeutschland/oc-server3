<?php

class SysTransTextEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $transId;

    /** @var string */
    public $lang;

    /** @var string */
    public $text;

    /** @var DateTime */
    public $lastModified;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->transId === null;
    }
}
