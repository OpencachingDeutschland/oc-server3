<?php

class FieldNoteEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var int */
    public $geocacheId;

    /** @var smallint */
    public $type;

    /** @var DateTime */
    public $date;

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
