<?php 

class GeoCacheWaypointPoolEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $wpOc;

    /** @var string */
    public $uuid;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->wpOc === null;
    }
}
