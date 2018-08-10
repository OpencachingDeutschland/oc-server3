<?php 

class SysReplExcludeEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var DateTime */
    public $datExclude;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->userId === null;
    }
}
