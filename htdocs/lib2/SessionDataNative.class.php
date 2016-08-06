<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Session data handling with build-in php session
 ***************************************************************************/
require_once 'SessionDataInterface.class.php';

/**
 * Class SessionDataNative
 * Not for productive usage!! Implementation not finished yet
 */
class SessionDataNative implements SessionDataInterface
{
    public $changed = false;
    public $values = array();
    public $session_initialized = false;

    public function __construct()
    {
        if (isset($_REQUEST['SESSION']) && $_REQUEST['SESSION'] != '') {
            $this->init_session();
        }
    }

    private function init_session()
    {
        global $opt;

        if ($this->session_initialized !== true) {
            session_name('SESSION');
            session_set_cookie_params($opt['session']['expire']['cookie'], $opt['session']['path'],
                $opt['session']['domain']);
            session_start();

            if ($opt['session']['check_referer']) {
                if (isset($_SERVER['REFERER'])) {
                    // TODO fix the following if statement, seems corrupted
                    if (strtolower(substr('http' + strstr($_SERVER['REFERER'], '://'), 0,
                            strlen($opt['page']['absolute_http_url']))) != strtolower($opt['page']['absolute_http_url'])
                    ) {
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

    private function createNewSession()
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
    }

    public function get($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public function is_set($name)
    {
        return isset($_SESSION[$name]);
    }

    public function un_set($name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
            $this->changed = true;
        }
    }

    public function header()
    {
        // is automatically sent
    }

    public function debug()
    {
        print_r($_SESSION);
        exit;
    }

    public function close()
    {
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
