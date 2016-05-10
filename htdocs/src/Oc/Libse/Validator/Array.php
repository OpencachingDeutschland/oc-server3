<?php

namespace Oc\Libse\Validator;

/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
class ArrayValidator
{
    private $values;

    public function __construct($values)
    {
        $this->values = $values;
    }

    public function isValid($value)
    {
        return in_array($value, $this->values);
    }
}
