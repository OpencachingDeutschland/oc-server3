<?php 

class GeoCacheAttribEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $icon;

    /** @var int */
    public $transId;

    /** @var int */
    public $groupId;

    /** @var int */
    public $selectable;

    /** @var int */
    public $category;

    /** @var int */
    public $searchDefault;

    /** @var int */
    public $default;

    /** @var string */
    public $iconLarge;

    /** @var string */
    public $iconNo;

    /** @var string */
    public $iconUndef;

    /** @var string */
    public $htmlDesc;

    /** @var int */
    public $htmlDescTransId;

    /** @var int */
    public $hidden;

    /** @var int */
    public $gcId;

    /** @var int */
    public $gcInc;

    /** @var string */
    public $gcName;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
