<?php 

class SavedTextsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $objectType;

    /** @var int */
    public $objectId;

    /** @var int */
    public $subtype;

    /** @var string */
    public $text;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
