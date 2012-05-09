<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Cookie handling
 ***************************************************************************/

	$cookie = new cookie();

class cookie
{
	var $changed = false;
	var $values = array();
	var $session_initalized = false;

	function cookie()
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			if (isset($_REQUEST['SESSION']) && $_REQUEST['SESSION'] != '')
			{
				$this->init_session();
			}
		}
		else
		{
			if (isset($_COOKIE[$opt['session']['cookiename'] . 'data']))
			{
				//get the cookievars-array
				$decoded = base64_decode($_COOKIE[$opt['session']['cookiename'] . 'data']);
				
				if ($decoded !== false)
				{
					$this->values = @unserialize($decoded);
					if (!is_array($this->values))
						$this->values = array();
				}
				else
					$this->values = array();
			}
		}
	}

	function init_session()
	{
		global $opt;

		if ($this->session_initalized != true)
		{
			session_name('SESSION');
			session_set_cookie_params($opt['session']['expire']['cookie'], $opt['session']['path'], $opt['session']['domain']);
			session_start();

			if ($opt['session']['check_referer'])
				if (isset($_SERVER['REFERER']))
					if (strtolower(substr($_SERVER['REFERER'], 0, strlen($opt['page']['absolute_url']))) != strtolower($opt['page']['absolute_url']))
						$this->createNewSession();

			if ((isset($_GET['SESSION']) || isset($_POST['SESSION'])) && count($_SESSION) > 0)
			{
				// comapre and set timestamp
				if (isset($_SESSION['lastcall']))
				{
					if (abs(time() - $_SESSION['lastcall']) > $opt['session']['expire']['url'])
					{
						$this->createNewSession();
					}
				}

				$_SESSION['lastcall'] = time();
			}

			$this->session_initalized = true;
		}
	}

	function createNewSession()
	{
		session_regenerate_id();
		$locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : '';
		foreach ($_SESSION AS $k => $v)
		{
			unset($_SESSION[$k]);
		}
		if ($locale != '')
			$_SESSION['locale'] = $locale;
	}

	function set($name, $value, $default=null)
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			if (!isset($_SESSION[$name]) || $_SESSION[$name] != $value)
			{
				if ($value == $default)
				{
					if (isset($_SESSION[$name]))
					{
						unset($_SESSION[$name]);
						$this->changed = true;
					}
				}
				else
				{
					$this->init_session();
					$_SESSION[$name] = $value;
					$this->changed = true;
				}
			}
		}
		else
		{
			if (!isset($this->values[$name]) || $this->values[$name] != $value)
			{
				if ($value == $default)
				{
					if (isset($this->values[$name]))
					{
						unset($this->values[$name]);
						$this->changed = true;
					}
				}
				else
				{
					$this->values[$name] = $value;
					$this->changed = true;
				}
			}
		}
	}
	
	function get($name, $default='')
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
		}
		else
		{
			return isset($this->values[$name]) ? $this->values[$name] : $default;
		}
	}

	function is_set($name)
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			return isset($_SESSION[$name]);
		}
		else
		{
			return isset($this->values[$name]);
		}
	}

	function un_set($name)
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			if (isset($_SESSION[$name]))
			{
				unset($_SESSION[$name]);
				$this->changed = true;
			}
		}
		else
		{
			if (isset($this->values[$name]))
			{
				unset($this->values[$name]);
				$this->changed = true;
			}
		}
	}

	function header()
	{
		global $opt;

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			// is autmatically sent
		}
		else
		{
			if ($this->changed == true)
			{
				if (count($this->values) == 0)
					setcookie($opt['session']['cookiename'] . 'data', false, time() + 31536000, $opt['session']['path'], $opt['session']['domain'], 0);
				else
					setcookie($opt['session']['cookiename'] . 'data', base64_encode(serialize($this->values)), time() + 31536000, $opt['session']['path'], $opt['session']['domain'], 0);
			}
		}
	}
	
	function debug()
	{
		global $opt;
		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			print_r($_SESSION);
		}
		else
		{
			print_r($this->values);
		}
		exit;
	}

	function close()
	{
		global $opt;
		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			if ($this->session_initalized == true)
			{
				if (count($_SESSION) == 0)
					@session_destroy();
				else
					session_write_close();
			}
		}
		else
		{
		}
	}
}
?>