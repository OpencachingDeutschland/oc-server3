<?php

class OkapiVarsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $var;

    /** @var string */
    public $value;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->var === null;
    }
}
