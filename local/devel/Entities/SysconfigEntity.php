<?php 

class SysconfigEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $name;

    /** @var string */
    public $value;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->name === null;
    }
}
