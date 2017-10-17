<?php

namespace Oc\Validator\Constraints;

use Oc\GeoCache\Enum\WaypointType;
use Oc\GeoCache\Exception\UnknownWaypointTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class WaypointValidator
 *
 * @package Oc\Validator\Constraints
 */
class WaypointValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $waypoint The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @return bool
     */
    public function validate($waypoint, Constraint $constraint)
    {
        $valid = true;

        try {
            WaypointType::guess($waypoint);
        } catch (UnknownWaypointTypeException $e) {
            $this->context->buildViolation($constraint->messageInvalid)
                ->setParameter('%waypoint%', $waypoint)
                ->setInvalidValue($waypoint)
                ->addViolation();
            $valid = false;
        }

        return $valid;
    }

    /**
     * Check if the given waypoint is an oc waypoint.
     *
     * @param string $waypoint
     *
     * @return bool True if it is an oc waypoint
     */
    protected function isOCWaypoint($waypoint)
    {
        return stripos($waypoint, 'oc') === 0;
    }

    /**
     * Check if the given waypoint is an gc waypoint.
     *
     * @param string $waypoint
     *
     * @return bool True if it is an gc waypoint
     */
    protected function isGCWaypoint($waypoint)
    {
        return stripos($waypoint, 'gc') === 0;
    }
}
