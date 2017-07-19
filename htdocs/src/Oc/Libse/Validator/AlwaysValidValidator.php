<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Libse\Validator;

class AlwaysValidValidator
{
    public function isValid($value)
    {
        return true;
    }
}
