<?php

namespace Oc\Validator\Constraints;

use Oc\GeoCache\Enum\WaypointType;
use Oc\GeoCache\Exception\UnknownWaypointTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WaypointValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $waypoint The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($waypoint, Constraint $constraint): bool
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
     */
    protected function isOCWaypoint(string $waypoint): bool
    {
        return stripos($waypoint, 'oc') === 0;
    }

    /**
     * Check if the given waypoint is an gc waypoint.
     */
    protected function isGCWaypoint(string $waypoint): bool
    {
        return stripos($waypoint, 'gc') === 0;
    }
}
