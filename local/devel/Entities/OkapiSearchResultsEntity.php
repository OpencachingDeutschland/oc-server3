<?php 

class OkapiSearchResultsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $setId;

    /** @var int */
    public $cacheId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->setId === null;
    }
}
