<?php

namespace Oc\FieldNotes\Validator\Constraints;

use Oc\FieldNotes\Enum\LogType as FieldNotesLogType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LogTypeValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated.
     * @param Constraint $constraint The constraint for the validation.
     */
    public function validate($value, Constraint $constraint): void
    {
        $guessedLogType = FieldNotesLogType::guess($value);

        if ($guessedLogType === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%logType%', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
