<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Session data handling with build-in php session
 ***************************************************************************/

namespace Oc\Session;

/**
 * Not for productive usage!! Implementation not finished yet
 */
class SessionDataNative implements SessionDataInterface
{
    /**
     * @var bool
     */
    public $changed = false;

    /**
     * @var array
     */
    public $values = array();

    /**
     * @var bool
     */
    public $session_initialized = false;

    public function __construct()
    {
        if (isset($_REQUEST['SESSION']) && $_REQUEST['SESSION'] !== '') {
            $this->initSession();
        }
    }

    private function initSession(): void
    {
        global $opt;

        if ($this->session_initialized !== true) {
            session_name('SESSION');
            session_set_cookie_params(
                $opt['session']['expire']['cookie'],
                $opt['session']['path'],
                $opt['session']['domain']
            );
            session_start();

            if ($opt['session']['check_referer']) {
                if (isset($_SERVER['REFERER'])) {
                    // TODO fix the following if statement, seems corrupted
                    if (stripos('http' + strstr($_SERVER['REFERER'], '://'), strtolower($opt['page']['absolute_http_url'])) !== 0
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
    
    private function createNewSession(): void
    {
        session_regenerate_id();
        $locale = $_SESSION['locale'] ?? '';
        foreach ($_SESSION as $k => $v) {
            unset($_SESSION[$k]);
        }
        if ($locale != '') {
            $_SESSION['locale'] = $locale;
        }
    }

    public function set(string $name, $value, $default = null): void
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

    public function get(string $name, $default = null)
    {
        return $_SESSION[$name] ?? $default;
    }

    public function is_set(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    public function un_set(string $name): void
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
            $this->changed = true;
        }
    }

    public function header(): void
    {
        // is automatically sent
    }

    public function close(): void
    {
        if ($this->session_initialized === true) {
            if (count($_SESSION) === 0) {
                try {
                    session_destroy();
                } catch (\Exception $e) {
                    // @todo implement logging
                }
            } else {
                session_write_close();
            }
        }
    }
}
