<?php 

class LogTypesTextEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $logTypesId;

    /** @var string */
    public $lang;

    /** @var string */
    public $textCombo;

    /** @var string */
    public $textListing;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
