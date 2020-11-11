<?php

namespace Oc\Validator\Constraints;

/**
 * @Annotation
 */
class PersistedWaypoint extends Waypoint
{
    /**
     * @var string
     */
    public $messageNotFound = 'oc.validator.constraints.persisted_waypoint.not_found';
}
