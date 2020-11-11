<?php

class ReplicationOverwritetypesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $table;

    /** @var string */
    public $field;

    /** @var string */
    public $uuidFieldname;

    /** @var int */
    public $backupfirst;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
