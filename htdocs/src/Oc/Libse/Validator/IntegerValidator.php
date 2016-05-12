<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Validator;

class IntegerValidator extends NumericValidator
{
    private $int_len;

    public function __construct($min = false, $max = false, $int_len = '+')
    {
        parent::__construct($min, $max);

        $this->int_len = $int_len;
    }

    protected function getMinValue()
    {
        return ~PHP_INT_MAX;
    }

    protected function getMaxValue()
    {
        return PHP_INT_MAX;
    }

    protected function getValidateRegex()
    {
        return '-?[0-9]' . $this->int_len . '$';
    }

    protected function getValue($value)
    {
        return (int) $value;
    }
}
