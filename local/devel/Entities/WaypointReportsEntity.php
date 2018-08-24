<?php

class WaypointReportsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $reportId;

    /** @var DateTime */
    public $dateReported;

    /** @var string */
    public $wpOc;

    /** @var string */
    public $wpExternal;

    /** @var string */
    public $source;

    /** @var int */
    public $gcwpProcessed;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->reportId === null;
    }
}
