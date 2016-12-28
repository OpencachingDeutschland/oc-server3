<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 ***************************************************************************/

namespace Oc\Libse\Validator;

class AlwaysValidValidator
{
    public function isValid($value)
    {
        return true;
    }
}
