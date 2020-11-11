<?php

class GeoCacheListsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $uuid;

    /** @var int */
    public $node;

    /** @var int */
    public $userId;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastModified;

    /** @var DateTime */
    public $lastAdded;

    /** @var DateTime */
    public $lastStateChange;

    /** @var string */
    public $name;

    /** @var int */
    public $isPublic;

    /** @var string */
    public $description;

    /** @var int */
    public $descHtmledit;

    /** @var string */
    public $password;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
