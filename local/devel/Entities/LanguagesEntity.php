<?php 

class LanguagesEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $short;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var string */
    public $nativeName;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /** @var int */
    public $listDefaultDe;

    /** @var int */
    public $listDefaultEn;

    /** @var int */
    public $isTranslated;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->short === null;
    }
}
