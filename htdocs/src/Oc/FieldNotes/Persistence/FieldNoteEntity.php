<?php

namespace Oc\FieldNotes\Persistence;

use DateTime;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheEntity;
use Oc\Repository\AbstractEntity;

class FieldNoteEntity extends AbstractEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $geocacheId;

    /**
     * @var int
     */
    public $type;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var string
     */
    public $text;

    /**
     * @var GeoCacheEntity
     */
    public $geoCache;

    /**
     * Checks if the entity is new.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
