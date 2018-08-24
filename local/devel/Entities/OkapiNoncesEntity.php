<?php 

class OkapiNoncesEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $consumerKey;

    /** @var string */
    public $nonceHash;

    /** @var int */
    public $timestamp;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->consumerKey === null;
    }
}
