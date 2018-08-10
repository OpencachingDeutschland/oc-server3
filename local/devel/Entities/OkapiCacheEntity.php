<?php 

class OkapiCacheEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $key;

    /** @var float */
    public $score;

    /** @var DateTime */
    public $expires;

    /** @var string */
    public $value;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->key === null;
    }
}
