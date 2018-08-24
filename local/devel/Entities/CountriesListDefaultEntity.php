<?php 

class CountriesListDefaultEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $lang;

    /** @var string */
    public $show;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->lang === null;
    }
}
