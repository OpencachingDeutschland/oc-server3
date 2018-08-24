<?php 

class OkapiTokensEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $key;

    /** @var string */
    public $secret;

    /** @var enum */
    public $tokenType;

    /** @var int */
    public $timestamp;

    /** @var int */
    public $userId;

    /** @var string */
    public $consumerKey;

    /** @var string */
    public $verifier;

    /** @var string */
    public $callback;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->key === null;
    }
}
