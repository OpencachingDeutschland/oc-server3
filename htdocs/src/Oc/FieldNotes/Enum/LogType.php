<?php

namespace Oc\FieldNotes\Enum;

use Oc\GeoCache\Enum\LogType as GeoCacheLogType;

/**
 * Class LogType
 */
class LogType
{
    /**
     * @var int
     */
    public const FOUND = GeoCacheLogType::FOUND;

    /**
     * @var int
     */
    public const NOT_FOUND = GeoCacheLogType::NOT_FOUND;

    /**
     * @var int
     */
    public const NOTE = GeoCacheLogType::NOTE;

    /**
     * @var int
     */
    public const NEEDS_MAINTENANCE = 1000;

    /**
     * @var array
     */
    private const FILE_LOG_TYPE_MAPPING = [
        'found it' => self::FOUND,
        "didn't find it" => self::NOT_FOUND,
        'write note' => self::NOTE,
        'needs maintenance' => self::NEEDS_MAINTENANCE,
        'owner maintenance' => self::NOTE,
    ];

    /**
     * Guesses the log type by the string representation provided by a field note file.
     */
    public static function guess(string $fileLogType): ?int
    {
        return self::FILE_LOG_TYPE_MAPPING[strtolower($fileLogType)] ?? null;
    }
}
