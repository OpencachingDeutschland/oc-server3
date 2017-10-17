<?php

namespace Oc\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Waypoint
 *
 * @package Oc\Validator\Constraints
 * @Annotation
 */
class Waypoint extends Constraint
{
    public $messageInvalid = 'oc.validator.constraints.waypoint.invalid';
}
