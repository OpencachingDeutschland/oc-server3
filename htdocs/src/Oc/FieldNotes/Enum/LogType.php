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
    public const FILE_LOG_TYPE_MAPPING = [
        'Found it' => self::FOUND,
        "Didn't find it" => self::NOT_FOUND,
        'Write note' => self::NOTE,
        'Needs Maintenance' => self::NEEDS_MAINTENANCE,
    ];

    /**
     * Guesses the log type by the string representation provided by a field note file.
     */
    public static function guess(string $fileLogType): ?int
    {
        $logType = null;

        if (array_key_exists($fileLogType, self::FILE_LOG_TYPE_MAPPING)) {
            $logType = self::FILE_LOG_TYPE_MAPPING[$fileLogType];
        }

        return $logType;
    }
}
