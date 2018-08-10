<?php 

class StatUserEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var smallint */
    public $found;

    /** @var smallint */
    public $notfound;

    /** @var smallint */
    public $note;

    /** @var smallint */
    public $hidden;

    /** @var smallint */
    public $willAttend;

    /** @var smallint */
    public $maintenance;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->userId === null;
    }
}
