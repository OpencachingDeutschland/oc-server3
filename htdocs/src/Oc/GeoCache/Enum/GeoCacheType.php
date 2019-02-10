<?php

namespace Oc\GeoCache\Enum;

class GeoCacheType
{
    /**
     * @var int
     */
    public const UNKNOWN = 1;

    /**
     * @var int
     */
    public const TRADITIONAL = 2;

    /**
     * @var int
     */
    public const MULTI = 3;

    /**
     * @var int
     */
    public const VIRTUAL = 4;

    /**
     * @var int
     */
    public const WEBCAM = 5;

    /**
     * @var int
     */
    public const EVENT = 6;

    /**
     * @var int
     */
    public const QUIZ = 7;

    /**
     * @var int
     */
    public const MATH = 8;

    /**
     * @var int
     */
    public const MOVING = 9;

    /**
     * @var int
     */
    public const DRIVE_IN = 10;

    /**
     * @var int[]
     */
    public const EVENT_TYPES = [
        self::EVENT,
    ];

    /**
     * Checks if the given geocache type is an event type.
     */
    public static function isEventType(int $geoCacheType): bool
    {
        return in_array($geoCacheType, self::EVENT_TYPES, true);
    }
}
