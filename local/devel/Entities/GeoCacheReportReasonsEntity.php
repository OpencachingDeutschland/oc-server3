<?php 

class GeoCacheReportReasonsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var int */
    public $order;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
