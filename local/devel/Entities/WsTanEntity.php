<?php 

class WsTanEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $session;

    /** @var string */
    public $tan;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->session === null;
    }
}
