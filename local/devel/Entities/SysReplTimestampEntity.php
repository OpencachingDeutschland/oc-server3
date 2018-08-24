<?php

class SysReplTimestampEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $data;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
