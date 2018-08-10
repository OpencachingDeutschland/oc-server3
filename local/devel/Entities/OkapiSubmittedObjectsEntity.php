<?php 

class OkapiSubmittedObjectsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $objectType;

    /** @var int */
    public $objectId;

    /** @var string */
    public $consumerKey;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->objectType === null;
    }
}
