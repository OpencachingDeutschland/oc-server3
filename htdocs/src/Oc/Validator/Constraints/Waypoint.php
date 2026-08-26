<?php

namespace Oc\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Waypoint extends Constraint
{
    /**
     * @var string
     */
    public string $messageInvalid = 'oc.validator.constraints.waypoint.invalid';
}
