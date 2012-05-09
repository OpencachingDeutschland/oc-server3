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

	function cookie()
	{
		global $opt;

		if (isset($_COOKIE[$opt['cookie']['name'] . 'data']))
		{
			//get the cookievars-array
			$decoded = base64_decode($_COOKIE[$opt['cookie']['name'] . 'data']);
			
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

	function set($name, $value)
	{
		if (!isset($this->values[$name]) || $this->values[$name] != $value)
		{
			$this->values[$name] = $value;
			$this->changed = true;
		}
	}
	
	function get($name)
	{
		return isset($this->values[$name]) ? $this->values[$name] : '';
	}

	function is_set($name)
	{
		return isset($this->values[$name]);
	}

	function un_set($name)
	{
		if (isset($this->values[$name]))
		{
			unset($this->values[$name]);
			$this->changed = true;
		}
	}

	function header()
	{
		global $opt;

		if ($this->changed == true)
		{
			if (count($this->values) == 0)
				setcookie($opt['cookie']['name'] . 'data', false, time() + 31536000, $opt['cookie']['path'], $opt['cookie']['domain'], 0);
			else
				setcookie($opt['cookie']['name'] . 'data', base64_encode(serialize($this->values)), time() + 31536000, $opt['cookie']['path'], $opt['cookie']['domain'], 0);
		}
	}
	
	function debug()
	{
		print_r($this->values);
		exit;
	}
}
?>