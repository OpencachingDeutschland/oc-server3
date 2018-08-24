<?php 

class OkapiConsumersEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $key;

    /** @var string */
    public $name;

    /** @var string */
    public $secret;

    /** @var string */
    public $url;

    /** @var string */
    public $email;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $bflags;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->key === null;
    }
}
