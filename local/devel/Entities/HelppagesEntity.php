<?php

class HelppagesEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $ocpage;

    /** @var string */
    public $language;

    /** @var string */
    public $helppage;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->ocpage === null;
    }
}
