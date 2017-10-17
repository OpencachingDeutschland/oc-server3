<?php

namespace Oc\Validator\Constraints;

/**
 * Class PersistedWaypoint
 *
 * @package Oc\Validator\Constraints
 * @Annotation
 */
class PersistedWaypoint extends Waypoint
{
    public $messageNotFound = 'oc.validator.constraints.persisted_waypoint.not_found';
}
