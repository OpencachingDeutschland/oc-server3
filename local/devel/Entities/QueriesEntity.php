<?php 

class QueriesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var string */
    public $name;

    /** @var string */
    public $options;

    /** @var DateTime */
    public $lastQueried;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
