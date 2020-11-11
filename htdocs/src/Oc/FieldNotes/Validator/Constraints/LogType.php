<?php

namespace Oc\FieldNotes\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LogType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'oc.field_notes.validator.constraints.log_type';
}
