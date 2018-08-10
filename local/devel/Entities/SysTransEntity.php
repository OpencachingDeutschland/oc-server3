<?php 

class SysTransEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $text;

    /** @var DateTime */
    public $lastModified;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
