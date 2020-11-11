<?php

class StatpicsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $tplpath;

    /** @var string */
    public $previewpath;

    /** @var string */
    public $description;

    /** @var int */
    public $transId;

    /** @var int */
    public $maxtextwidth;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
