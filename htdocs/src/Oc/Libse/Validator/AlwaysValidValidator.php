<?php

namespace Oc\Libse\Validator;

class AlwaysValidValidator
{
    public function isValid($value)
    {
        return true;
    }
}
