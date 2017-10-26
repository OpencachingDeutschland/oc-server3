<?php

namespace Oc\GeoCache\Enum;

use Oc\GeoCache\Exception\UnknownWaypointTypeException;

class WaypointType
{
    /**
     * Opencaching waypoint.
     *
     * @var string
     */
    const OC = 'oc';

    /**
     * Geocaching waypoint.
     *
     * @var string
     */
    const GC = 'gc';

    /**
     * Guesses the waypoint type by given waypoint.
     *
     * @param string $waypoint
     *
     * @return string
     *
     * @throws UnknownWaypointTypeException Thrown when the waypoint type could not be guessed
     */
    public static function guess($waypoint)
    {
        $waypointType = null;

        if (stripos($waypoint, self::OC) === 0) {
            $waypointType = self::OC;
        } elseif (stripos($waypoint, self::GC) === 0) {
            $waypointType = self::GC;
        }

        if ($waypointType === null) {
            throw new UnknownWaypointTypeException(
                sprintf(
                    'Could not guess the waypoint type of the waypoint "%s"',
                    $waypoint
                )
            );
        }

        return $waypointType;
    }
}
