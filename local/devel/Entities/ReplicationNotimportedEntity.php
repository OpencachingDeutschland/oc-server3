<?php 

class ReplicationNotimportedEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $objectUuid;

    /** @var int */
    public $objectType;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
