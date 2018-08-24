<?php 

class SysTransRefEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $transId;

    /** @var string */
    public $resourceName;

    /** @var int */
    public $line;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->transId === null;
    }
}
