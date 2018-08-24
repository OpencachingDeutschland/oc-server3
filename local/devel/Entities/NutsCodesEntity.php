<?php 

class NutsCodesEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $code;

    /** @var string */
    public $name;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->code === null;
    }
}
