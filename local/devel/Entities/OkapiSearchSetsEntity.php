<?php 

class OkapiSearchSetsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $paramsHash;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $expires;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
