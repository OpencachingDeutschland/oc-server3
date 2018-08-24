<?php 

class OkapiAuthorizationsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $consumerKey;

    /** @var int */
    public $userId;

    /** @var DateTime */
    public $lastAccessToken;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->consumerKey === null;
    }
}
