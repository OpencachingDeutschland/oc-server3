<?php

namespace Oc\GeoCache\Enum;

class GeoCacheType
{
    /**
     * @var int
     */
    const UNKNOWN = 1;

    /**
     * @var int
     */
    const TRADITIONAL = 2;

    /**
     * @var int
     */
    const MULTI = 3;

    /**
     * @var int
     */
    const VIRTUAL = 4;

    /**
     * @var int
     */
    const WEBCAM = 5;

    /**
     * @var int
     */
    const EVENT = 6;

    /**
     * @var int
     */
    const QUIZ = 7;

    /**
     * @var int
     */
    const MATH = 8;

    /**
     * @var int
     */
    const MOVING = 9;

    /**
     * @var int
     */
    const DRIVE_IN = 10;

    /**
     * @var int[]
     */
    const EVENT_TYPES = [
        self::EVENT
    ];

    /**
     * Checks if the given geocache type is an event type.
     *
     * @param int $geoCacheType
     *
     * @return bool Returns true if this type is an event type
     */
    public static function isEventType($geoCacheType)
    {
        return in_array($geoCacheType, self::EVENT_TYPES, true);
    }
}
