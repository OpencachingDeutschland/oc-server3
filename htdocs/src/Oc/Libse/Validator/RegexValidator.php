<?php
/***************************************************************************
 * for license information see doc/license.txt
 ***************************************************************************/

namespace Oc\Libse\Validator;

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
