<?php 

class PageBlockEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $pageId;

    /** @var string */
    public $locale;

    /** @var string */
    public $title;

    /** @var string */
    public $html;

    /** @var int */
    public $position;

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
