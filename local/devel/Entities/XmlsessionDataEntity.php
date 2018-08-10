<?php 

class XmlsessionDataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $sessionId;

    /** @var int */
    public $objectType;

    /** @var int */
    public $objectId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->sessionId === null;
    }
}
