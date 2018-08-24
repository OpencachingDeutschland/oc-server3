<?php 

class SearchIndexEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $objectType;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $hash;

    /** @var int */
    public $count;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->objectType === null;
    }
}
