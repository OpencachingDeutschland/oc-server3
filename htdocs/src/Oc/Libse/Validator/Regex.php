<?php

namespace Oc\Libse\Validator;

/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
class RegexValidator
{
    private $regex;

    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    public function isValid($value)
    {
        return mb_ereg_match($this->regex, $value);
    }
}
