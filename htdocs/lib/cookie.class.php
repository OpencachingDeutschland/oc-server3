<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Cookie handling
 ***************************************************************************/

$cookie = new cookie();

class cookie
{
    public $changed = false;
    public $values = array();

    public function __construct()
    {
        global $opt;

        if (isset($_COOKIE[$opt['cookie']['name'] . 'data'])) {
            //get the cookievars-array
            $decoded = base64_decode($_COOKIE[$opt['cookie']['name'] . 'data']);

            if ($decoded !== false) {
                $this->values = @unserialize($decoded);
                if (!is_array($this->values)) {
                    $this->values = array();
                }
            } else {
                $this->values = array();
            }
        }
    }

    public function set($name, $value)
    {
        if (!isset($this->values[$name]) || $this->values[$name] != $value) {
            $this->values[$name] = $value;
            $this->changed = true;
        }
    }

    public function get($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : '';
    }

    public function is_set($name)
    {
        return isset($this->values[$name]);
    }

    public function un_set($name)
    {
        if (isset($this->values[$name])) {
            unset($this->values[$name]);
            $this->changed = true;
        }
    }

    public function header()
    {
        global $opt;

        if ($this->changed == true) {
            if (count($this->values) == 0) {
                setcookie(
                    $opt['cookie']['name'] . 'data',
                    false,
                    time() + 31536000,
                    $opt['cookie']['path'],
                    $opt['cookie']['domain'],
                    0
                );
            } else {
                setcookie(
                    $opt['cookie']['name'] . 'data',
                    base64_encode(serialize($this->values)),
                    time() + 31536000,
                    $opt['cookie']['path'],
                    $opt['cookie']['domain'],
                    0
                );
            }
        }
    }

    public function debug()
    {
        print_r($this->values);
        exit;
    }
}
