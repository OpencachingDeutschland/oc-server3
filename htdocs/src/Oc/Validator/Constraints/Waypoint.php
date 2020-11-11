<?php

namespace Oc\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Waypoint extends Constraint
{
    /**
     * @var string
     */
    public $messageInvalid = 'oc.validator.constraints.waypoint.invalid';
}
