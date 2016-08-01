<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Session data handling with cookies
 *  See doc/cookies.txt for more information in cookies.
 ***************************************************************************/
require_once 'SessionDataInterface.class.php';

class SessionDataCookie implements SessionDataInterface
{
    public $changed = false;
    public $values = array();

    public function __construct()
    {
        global $opt;

        if (isset($_COOKIE[$opt['session']['cookiename'] . 'data'])) {
            //get the cookievars-array
            $decoded = base64_decode($_COOKIE[$opt['session']['cookiename'] . 'data']);

            if ($decoded !== false) {
                // TODO replace by safe function
                $this->values = @unserialize($decoded);
                if (!is_array($this->values)) {
                    $this->values = array();
                }
            } else {
                $this->values = array();
            }
        }
    }

    public function set($name, $value, $default = null)
    {
        // Store cookie value in internal array. OcSmarty will call this->header()
        // to actually set the cookie.
        if (!isset($this->values[$name]) || $this->values[$name] != $value) {
            if ($value == $default) {
                if (isset($this->values[$name])) {
                    unset($this->values[$name]);
                    $this->changed = true;
                }
            } else {
                $this->values[$name] = $value;
                $this->changed = true;
            }
        }
    }

    public function get($name, $default = null)
    {
        return isset($this->values[$name]) ? $this->values[$name] : $default;
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

        if ($this->changed === true) {
            if (count($this->values) === 0) {
                setcookie(
                    $opt['session']['cookiename'] . 'data',
                    false,
                    time() + 31536000,
                    $opt['session']['path'],
                    $opt['session']['domain'],
                    0
                );
            } else {
                setcookie(
                    $opt['session']['cookiename'] . 'data',
                    // TODO replace by safe function
                    base64_encode(serialize($this->values)),
                    time() + 31536000,
                    $opt['session']['path'],
                    $opt['session']['domain'],
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

    public function close()
    {
        // TODO really nothing?
        // maybe destroy cookies here
    }
}
