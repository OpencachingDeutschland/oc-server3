<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Cookie handling
 *  See doc/cookies.txt for more information in cookies.
 ***************************************************************************/

$cookie = new cookie();

class cookie
{
    public $changed = false;
    public $values = array();
    public $session_initialized = false;

    public function __construct()
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            if (isset($_REQUEST['SESSION']) && $_REQUEST['SESSION'] != '') {
                $this->init_session();
            }
        } elseif (isset($_COOKIE[$opt['session']['cookiename'] . 'data'])) {
            // get the cookievars-array
            // returns false in strict mode, if not valid base64 input
            $decoded = base64_decode($_COOKIE[$opt['session']['cookiename'] . 'data'], true);

            if ($decoded !== false) {
                //$this->values = @unserialize($decoded); // not secure with user input
                $this->values = @json_decode($decoded, true, 2);
                if (!is_array($this->values)) {
                    $this->values = array();
                }
            } else {
                $this->values = array();
            }
        }
    }

    public function init_session()
    {
        global $opt;

        if ($this->session_initialized !== true) {
            session_name('SESSION');
            session_set_cookie_params($opt['session']['expire']['cookie'], $opt['session']['path'], $opt['session']['domain']);
            session_start();

            if ($opt['session']['check_referer']) {
                if (isset($_SERVER['REFERER'])) {
                    // TODO fix the following if statement, seems corrupted
                    if (strtolower(substr('http' + strstr($_SERVER['REFERER'], '://'), 0, strlen($opt['page']['absolute_http_url']))) != strtolower($opt['page']['absolute_http_url'])) {
                        $this->createNewSession();
                    }
                }
            }

            if ((isset($_GET['SESSION']) || isset($_POST['SESSION'])) && count($_SESSION) > 0) {
                // compare and set timestamp
                if (isset($_SESSION['lastcall'])) {
                    if (abs(time() - $_SESSION['lastcall']) > $opt['session']['expire']['url']) {
                        $this->createNewSession();
                    }
                }

                $_SESSION['lastcall'] = time();
            }

            $this->session_initialized = true;
        }
    }

    public function createNewSession()
    {
        session_regenerate_id();
        $locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : '';
        foreach ($_SESSION as $k => $v) {
            unset($_SESSION[$k]);
        }
        if ($locale != '') {
            $_SESSION['locale'] = $locale;
        }
    }

    public function set($name, $value, $default = null)
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            if (!isset($_SESSION[$name]) || $_SESSION[$name] != $value) {
                if ($value == $default) {
                    if (isset($_SESSION[$name])) {
                        unset($_SESSION[$name]);
                        $this->changed = true;
                    }
                } else {
                    $this->init_session();
                    $_SESSION[$name] = $value;
                    $this->changed = true;
                }
            }
        } else {
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
    }

    public function get($name, $default = '')
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
        } else {
            return isset($this->values[$name]) ? $this->values[$name] : $default;
        }
    }

    public function is_set($name)
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            return isset($_SESSION[$name]);
        } else {
            return isset($this->values[$name]);
        }
    }

    public function un_set($name)
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            if (isset($_SESSION[$name])) {
                unset($_SESSION[$name]);
                $this->changed = true;
            }
        } else {
            if (isset($this->values[$name])) {
                unset($this->values[$name]);
                $this->changed = true;
            }
        }
    }

    public function header()
    {
        global $opt;

        if ($opt['session']['mode'] == SAVE_SESSION) {
            // is automatically sent
        } else {
            if ($this->changed == true) {
                if (count($this->values) == 0) {
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
                        //base64_encode(serialize($this->values)), // serialize not secure with external client data
                        base64_encode(json_encode($this->values)),
                        time() + 31536000,
                        $opt['session']['path'],
                        $opt['session']['domain'],
                        0
                    );
                }
            }
        }
    }

    public function debug()
    {
        global $opt;
        if ($opt['session']['mode'] == SAVE_SESSION) {
            print_r($_SESSION);
        } else {
            print_r($this->values);
        }
        exit;
    }

    public function close()
    {
        global $opt;
        if ($opt['session']['mode'] == SAVE_SESSION) {
            if ($this->session_initialized === true) {
                if (count($_SESSION) === 0) {
                    try {
                        session_destroy();
                    } catch (Exception $e) {
                        // @todo implement logging
                    }
                } else {
                    session_write_close();
                }
            }
        }
    }
}
