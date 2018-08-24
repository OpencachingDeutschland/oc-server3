<?php 

class GeoCacheReportsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $cacheid;

    /** @var int */
    public $userid;

    /** @var int */
    public $reason;

    /** @var string */
    public $note;

    /** @var int */
    public $status;

    /** @var int */
    public $adminid;

    /** @var string */
    public $lastmodified;

    /** @var string */
    public $comment;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
