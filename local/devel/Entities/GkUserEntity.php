<?php 

class GkUserEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
