<?php

namespace Oc\FieldNotes\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class DateTime extends Constraint
{
    /**
     * @var string
     */
    public string $message = 'oc.field_notes.validator.constraints.date_time';
}
