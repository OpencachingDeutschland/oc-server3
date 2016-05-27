<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Http;

class RequestHttp
{

    private $raw_values = [];

    private $valid_values = [];

    public function __construct()
    {
        if (!empty($_POST)) {
            $this->raw_values = $_POST;
        } elseif (!empty($_GET)) {
            $this->raw_values = $_GET;
        }
    }

    public function get($key, $defaultValue = '')
    {
        return self::getValue($this->valid_values, $key, $defaultValue);
    }

    public function getForValidation($key, $defaultValue = '')
    {
        return self::getValue($this->raw_values, $key, $defaultValue);
    }

    private static function getValue($values, $key, $defaultValue)
    {
        if (array_key_exists($key, $values)) {
            return $values[$key];
        }

        return $defaultValue;
    }

    public function validate($key, $validator)
    {
        $value = $this->getForValidation($key);

        if ($validator->isValid($value)) {
            $this->set($key, $value);

            return true;
        }

        return false;
    }

    public function set($key, $value)
    {
        $this->valid_values[$key] = $value;
    }

    public function setForValidation($key, $value)
    {
        $this->raw_values[$key] = $value;
    }
}
