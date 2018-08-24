<?php 

class SysMenuEntity extends Oc\Repository\AbstractEntity
{
    /** @var smallint */
    public $id;

    /** @var string */
    public $idString;

    /** @var string */
    public $title;

    /** @var int */
    public $titleTransId;

    /** @var string */
    public $menustring;

    /** @var int */
    public $menustringTransId;

    /** @var int */
    public $access;

    /** @var string */
    public $href;

    /** @var int */
    public $visible;

    /** @var smallint */
    public $parent;

    /** @var int */
    public $position;

    /** @var string */
    public $color;

    /** @var int */
    public $sitemap;

    /** @var int */
    public $onlyIfParent;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
