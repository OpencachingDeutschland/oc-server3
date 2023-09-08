<?php

namespace Oc\FieldNotes\Validator\Constraints;

use DateTime as PHPDateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    public const FORMAT_LONG = 'Y-m-d\TH:i:s\Z';

    /**
     * @var string
     */
    public const FORMAT_LONG_EXPANDED = 'YYYY-MM-DDThh:mm:ssZ';

    /**
     * @var string
     */
    public const FORMAT_SHORT = 'Y-m-d\TH:i\Z';

    /**
     * @var string
     */
    public const FORMAT_SHORT_EXPANDED = 'YYYY-MM-DDThh:mmZ';

    private const VAlID_FORMATS = [
        self::FORMAT_LONG,
        self::FORMAT_SHORT,
        'Y-m-d\TH:i:s.v\Z',
        'c',
    ];

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated.
     * @param Constraint $constraint The constraint for the validation.
     */
    public function validate($value, Constraint $constraint): void
    {
        $valid = false;

        foreach (self::VAlID_FORMATS as $format) {
            if (false !== PHPDateTime::createFromFormat($format, $value)) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%datetime%', $value)
                ->setParameter('%expectedFormatLong%', self::FORMAT_LONG_EXPANDED)
                ->setParameter('%expectedFormatShort%', self::FORMAT_SHORT_EXPANDED)
                ->addViolation();
        }
    }
}
