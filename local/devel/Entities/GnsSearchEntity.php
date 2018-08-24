<?php 

class GnsSearchEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $uniId;

    /** @var string */
    public $sort;

    /** @var string */
    public $simple;

    /** @var int */
    public $simplehash;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->uniId === null;
    }
}
