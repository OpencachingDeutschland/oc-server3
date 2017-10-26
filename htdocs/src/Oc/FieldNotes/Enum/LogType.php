<?php

namespace Oc\FieldNotes\Enum;

use Oc\GeoCache\Enum\LogType as GeoCacheLogType;

/**
 * Class LogType
 *
 * @package Oc\FieldNotes\Enum
 */
class LogType
{
    /**
     * @var int
     */
    const FOUND = GeoCacheLogType::FOUND;

    /**
     * @var int
     */
    const NOT_FOUND = GeoCacheLogType::NOT_FOUND;

    /**
     * @var int
     */
    const NOTE = GeoCacheLogType::NOTE;

    /**
     * @var int
     */
    const NEEDS_MAINTENANCE = 1000;


    const FILE_LOG_TYPE_MAPPING = [
        'Found it' => self::FOUND,
        "Didn't find it" => self::NOT_FOUND,
        'Write note' => self::NOTE,
        'Needs Maintenance' => self::NEEDS_MAINTENANCE
    ];

    /**
     * Guesses the log type by the string representation provided by a field note file.
     *
     * @param string $fileLogType
     *
     * @return int|null
     */
    public static function guess($fileLogType)
    {
        $logType = null;

        if (array_key_exists($fileLogType, self::FILE_LOG_TYPE_MAPPING)) {
            $logType = self::FILE_LOG_TYPE_MAPPING[$fileLogType];
        }

        return $logType;
    }
}
