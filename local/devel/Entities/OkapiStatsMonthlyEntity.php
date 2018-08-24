<?php

class OkapiStatsMonthlyEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $consumerKey;

    /** @var int */
    public $userId;

    /** @var DateTime */
    public $periodStart;

    /** @var string */
    public $serviceName;

    /** @var int */
    public $totalCalls;

    /** @var int */
    public $httpCalls;

    /** @var float */
    public $totalRuntime;

    /** @var float */
    public $httpRuntime;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->consumerKey === null;
    }
}
