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
            echo $decoded = base64_decode($_COOKIE[$opt['session']['cookiename'] . 'data'], true);

            if ($decoded !== false) {
                // TODO replace by safe function
                $this->values = @unserialize($decoded); // bad
                //$this->values = @json_decode($decoded, true); // safe
                //print_r($this->values);
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

            $value = null;
            if (count($this->values) > 0) {
                // TODO replace by safe function
                $value = base64_encode(serialize($this->values)); // bad
                //$value = base64_encode(json_encode($this->values)); // safe
            } else {
                $value = false;
            }
            // https used for request and https is available, then set cookie https only
            $https_session = $opt['page']['https']['active']
                && $opt['page']['https']['mode'] != HTTPS_DISABLED
                && $this->is_set('sessionid') // only force https while login
                && !empty($this->get('sessionid'));

            setcookie(
                $opt['session']['cookiename'] . 'data',
                $value,
                time() + 365 * 24 * 60 * 60,
                $opt['session']['path'],
                $opt['session']['domain'],
                $https_session // https only?
            );

            // if site is requested by http no session data is visible, so set cookie as flag to redirect to https
            setcookie(
                $opt['session']['cookiename'] . 'https_session',
                $https_session,
                time() + 365 * 24 * 60 * 60,
                $opt['session']['path'],
                $opt['session']['domain'],
                0, // must be available for http
                1 // communication only, no js
            );
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
        // maybe destroy variables here
    }
}
