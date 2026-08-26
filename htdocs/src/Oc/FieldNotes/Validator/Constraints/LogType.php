<?php

namespace Oc\FieldNotes\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class LogType extends Constraint
{
    /**
     * @var string
     */
    public string $message = 'oc.field_notes.validator.constraints.log_type';
}
