<?php

namespace Oc\Validator\Constraints;

use Oc\GeoCache\Persistence\GeoCache\GeoCacheService;
use Symfony\Component\Validator\Constraint;

class PersistedWaypointValidator extends WaypointValidator
{
    /**
     * @var GeoCacheService
     */
    private $geoCacheService;

    public function __construct(GeoCacheService $geoCacheService)
    {
        $this->geoCacheService = $geoCacheService;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $waypoint The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($waypoint, Constraint $constraint): bool
    {
        $valid = parent::validate($waypoint, $constraint);

        if (!$valid) {
            return false;
        }

        $geoCache = $this->geoCacheService->fetchByWaypoint($waypoint);

        if ($geoCache === null) {
            $this->context->buildViolation($constraint->messageNotFound)
                ->setParameter('%waypoint%', $waypoint)
                ->addViolation();
        }

        return true;
    }
}
