<?php 

class ReplicationOverwriteEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $type;

    /** @var string */
    public $value;

    /** @var string */
    public $uuid;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
