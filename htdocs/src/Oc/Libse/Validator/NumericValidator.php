<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Validator;

abstract class NumericValidator
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        if ($min !== false) {
            $this->min = $min;
        } else {
            $this->min = $this->getMinValue();
        }

        if ($max !== false) {
            $this->max = $max;
        } else {
            $this->max = $this->getMaxValue();
        }

        if ($this->min > $this->max) {
            throw new \InvalidArgumentException();
        }
    }

    abstract protected function getMinValue();

    abstract protected function getMaxValue();

    public function isValid($value)
    {
        if (!mb_ereg_match($this->getValidateRegex(), $value)) {
            return false;
        }

        $num_value = $this->getValue($value);

        return $this->min <= $num_value && $num_value <= $this->max;
    }

    abstract protected function getValidateRegex();

    abstract protected function getValue($value);
}
