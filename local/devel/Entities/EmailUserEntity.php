<?php 

class EmailUserEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var string */
    public $ipaddress;

    /** @var int */
    public $fromUserId;

    /** @var string */
    public $fromEmail;

    /** @var int */
    public $toUserId;

    /** @var string */
    public $toEmail;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
