<?php 

class PageEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $slug;

    /** @var string */
    public $metaKeywords;

    /** @var string */
    public $metaDescription;

    /** @var string */
    public $metaSocial;

    /** @var DateTime */
    public $updatedAt;

    /** @var int */
    public $active;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
