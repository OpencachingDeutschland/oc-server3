<?php

class OkapiStatsTempEntity extends Oc\Repository\AbstractEntity
{
    /** @var DateTime */
    public $datetime;

    /** @var string */
    public $consumerKey;

    /** @var int */
    public $userId;

    /** @var string */
    public $serviceName;

    /** @var enum */
    public $calltype;

    /** @var float */
    public $runtime;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->datetime === null;
    }
}
