<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Validator;

class RealValidator extends NumericValidator
{
    private $int_len;
    private $dec_len;

    public function __construct($min = false, $max = false, $int_len = '+', $dec_len = '+')
    {
        parent::__construct($min, $max);

        $this->int_len = $int_len;
        $this->dec_len = $dec_len;
    }

    protected function getMinValue()
    {
        return - 1e38;
    }

    protected function getMaxValue()
    {
        return 1e38;
    }

    protected function getValidateRegex()
    {
        return '-?[0-9]' . $this->int_len . '([,.][0-9]' . $this->dec_len . ')?$';
    }

    protected function getValue($value)
    {
        $value = str_replace(',', '.', $value);

        return (float)$value;
    }
}
