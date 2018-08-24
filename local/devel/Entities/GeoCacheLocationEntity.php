<?php 

class GeoCacheLocationEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $lastModified;

    /** @var string */
    public $adm1;

    /** @var string */
    public $adm2;

    /** @var string */
    public $adm3;

    /** @var string */
    public $adm4;

    /** @var string */
    public $code1;

    /** @var string */
    public $code2;

    /** @var string */
    public $code3;

    /** @var string */
    public $code4;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
