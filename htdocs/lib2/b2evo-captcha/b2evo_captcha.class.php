<?php
/**
  * PHP Class b2evo_captcha Version 1.3.1, released 27-Jan-2006
  *
  * a PHP Class for creating and testing captchas used in b2evolution
  *
  * Author : Ben Franske, ben@franske.com, http://ben.franske.com
  *
  * Based on hn_captcha Version 1.2 by Horst Nogajski, horst@nogajski.de
  *     - hn_captcha is a fork of ocr_captcha by Julien Pachet
  * 
  * License: GNU GPL (http://www.opensource.org/licenses/gpl-license.html)
  *
  **/

/**
  *
  * changes in version 1.3.1:
  *  - removed unrequired double quotes
  *  - use function_exists() to check for some required functions
  *
  * changes in version 1.3:
  *  - modified for use in b2evolution and to make more of a standalone class:
  *      - stripped code so only image generation and testing remain
  *      - removed code for multiple attempts, one shot per image only (K.I.S.S.)
  *      - automatically select from multiple random fonts from the fonts folder
  *	 - support for random captcha length
  *      - support for easily selecting valid characters and number of characters
  *	 - added built-in garbage cleanup
  *	 - support for case sensitive captchas
  *	 - upgraded from rand() functions to mt_rand() functions
  *	 - support for full md5 hashes instead of hash substrings
  *	 - made it easier to drop in different image generation function
  *
  * changes in version 1.2:
  *  - added a new configuration-variable: secretposition
  *  - once more modified the function get_try(): generate a string of 32 chars length,
  *    where at secretposition is the number of current-try.
  *    Hopefully this is enough for hackprevention.
  *
  * changes in version 1.1:
  *  - added a new configuration-variable: maxrotation
  *  - added a new configuration-variable: secretstring
  *  - modified function get_try(): now ever returns a string of 16 chars
  *
  **/

/**
  * License: GNU GPL (http://www.opensource.org/licenses/gpl-license.html)
  * 
  * This program is free software;
  * 
  * you can redistribute it and/or modify it under the terms of the GNU General Public License
  * as published by the Free Software Foundation; either version 2 of the License,
  * or (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
  * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License along with this program;
  * if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  *
  **/


class b2evo_captcha
{

	////////////////////////////////
	//
	//	Default options, can be overridden from the calling code
	//

	var $public_key='';
	var $prefix = '';

	/**
	 * Absolute path to a Tempfolder (with trailing slash!). This must be writeable for PHP and also accessible via HTTP, because the image will be stored there.
	 *
	 **/
	var $tempfolder;

	/**
	 * Absolute path to folder with TrueTypeFonts (with trailing slash!). This must be readable by PHP.
	 *
	 **/
	var $TTF_folder;

	/**
         * The minimum number of characters to use for the captcha
	 * Set to the same as maxchars to use fixed length captchas
         **/
	var $minchars = 5;

	/**
         * The maximum number of characters to use for the captcha
	 * Set to the same as minchars to use fixed length captchas
         **/
	var $maxchars = 7;

	/**
         * The minimum font size to use
         *
	 **/
	var $minsize = 20;

	/**
          * The maximum font size to use
          *
          **/
	var $maxsize = 30;

	/**
          * The maximum degrees a Char should be rotated. Set it to 30 means a random rotation between -30 and 30.
          *
          **/
	var $maxrotation = 25;

	/**
          * Background noise On/Off (if is FALSE, a grid will be created)
          *
          **/
	var $noise = TRUE;

	/**
          * This will only use the 216 websafe color pallette for the image.
          *
          **/
	var $websafecolors = FALSE;

	/**
          * Outputs configuration values for testing
          *
          **/
	var $debug = FALSE;

	/**
          * Filename of garbage collector counter which is stored in the tempfolder
          *
          **/
	var $counter_filename = 'b2evo_captcha_counter.txt';

	/**
          * Prefix of captcha image filenames
          *
          **/
	var $filename_prefix = 'b2evo_captcha_';

	/**
          * Number of captchas to generate before garbage collection is done
          *
          **/
	var $collect_garbage_after = 100;

	/**
          * Maximum lifetime of a captcha (in seconds) before being deleted during garbage collection
          *
          **/
	var $maxlifetime = 600;

	/**
          * Make all letters uppercase (does not preclude symbols)
          *
          **/
	var $case_sensitive = TRUE;

	////////////////////////////////
	//
	//	Private options, these are fixed options
	//

	/**
          * String of valid characters which may appear in the captcha
          *
          **/
	var $validchars = 'abcdefghjkmnpqrstuvwxyz23456789?@#$%&*ABCDEFGHJKLMNPQRSTUVWXYZ23456789?@#$%&*';

	/**
          * Picture width
          *
          **/
	var $lx;

	/**
          * Picture height
          *
          **/
	var $ly;

	/**
          * JPEG Image quality
          *
          **/
	var $jpegquality = 80;

	/**
          * Noise multiplier (number of characters gets multipled by this to define noise)
          * Note: This doesn't quite make sense, do you really want less noise in a smaller captcha?
          **/
	var $noisefactor = 9;

	/**
          * Number of backgrond noise characters
          *
          **/
	var $nb_noise;

	/**
          * Holds the list of possible fonts
          *
          **/
	var $TTF_RANGE;

	/**
          * Holds the currently selected font filename
          *
          **/
	var $TTF_file;

	/**
          * Holds the number of characters in the captcha
          *
          **/
	var $chars;

	var $public_K;
	var $private_K;

	/**
          * Captcha filename
          *
          **/
	var $filename;

	/**
          * Holds the version number of the GD-Library
          *
          **/
	var $gd_version;

	var $r;
	var $g;
	var $b;


	////////////////////////////////
	//
	//	CONSTRUCTOR
	//

		/**
		  * Extracts the config array and overrides default settings.
		  *
		  **/
		function b2evo_captcha($config,$secure=TRUE)
		{

			// Test for GD-Library(-Version)
			$this->gd_version = $this->get_gd_version();
			if($this->gd_version == 0) die('There is no GD-Library-Support enabled. The b2evo captcha class cannot be used!');
			if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: The available GD-Library has major version ".$this->gd_version;

			// extracts config array
			if(is_array($config))
			{
				if($secure && (!function_exists('version_compare') || version_compare(phpversion(), '4.2.0', '< ')) && function_exists(array_key_exists))
				{
					if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Extracts Config-Array in secure-mode!";
					$valid = get_class_vars(get_class($this));
					foreach($config as $k=>$v)
					{
						if(array_key_exists($k,$valid)) $this->$k = $v;
					}
				}
				else
				{
					if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Extracts Config-Array in unsecure-mode!";
					foreach($config as $k=>$v) $this->$k = $v;
				}
			}

			// check vars for min-max-chars and min-max-size
			if($this->minchars > $this->maxchars)
			{
				$temp = $this->minchars;
				$this->minchars = $this->maxchars;
				$this->maxchars = $temp;
				if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Arrghh! What do you think I mean with min and max? Switch minchars with maxchars.";
			}
			if($this->minsize > $this->maxsize)
			{
				$temp = $this->minsize;
				$this->minsize = $this->maxsize;
				$this->maxsize = $temp;
				if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Arrghh! What do you think I mean with min and max? Switch minsize with maxsize.";
			}


			// check TrueTypeFonts
			$this->TTF_RANGE = array('0');
			if ($handle = opendir($this->TTF_folder)) {
				$i=0;
   				while (false !== ($file = readdir($handle))) {
					//You could add a regex to this if to make sure the files are all *.ttf
				       	if ($file != '.' && $file != '..') {
				       		if (is_file($this->TTF_folder . $file)) {
								$this->TTF_RANGE[$i]=$file;
					        	if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Found font file (".$file.')';
						}
					}
				}
				closedir($handle);
			}
			if(is_array($this->TTF_RANGE))
			{
				if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Checking given TrueType-Array! (".count($this->TTF_RANGE).')';
				$temp = array();
				foreach($this->TTF_RANGE as $k=>$v)
				{
					if(is_readable($this->TTF_folder.$v)) $temp[] = $v;
				}
				$this->TTF_RANGE = $temp;
				if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Valid TrueType-files: (".count($this->TTF_RANGE).')';
				if(count($this->TTF_RANGE) < 1) die('No Truetype fonts available for the CaptchaClass.');
			}
			else
			{
				if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Check given TrueType-File! (".$this->TTF_RANGE.')';
				if(!is_readable($this->TTF_folder.$this->TTF_RANGE)) die('No Truetypefont available for the b2evo captcha class.');
			}

			// select first TrueTypeFont
			$this->change_TTF();
			if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Set current TrueType-File: (".$this->TTF_file.")";


			// get number of noise-chars for background if is enabled
			$this->nb_noise = $this->noise ? ($this->chars * $this->noisefactor) : 0;
			if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Set number of noise characters to: (".$this->nb_noise.')';

			// seed the random number generator if less than php 4.2.0
			if( !function_exists('version_compare') || version_compare(phpversion(), '4.2.0', '< ') )
			{
			        mt_srand((double)microtime()*1000000);
			}

			// specify counter-filename
			if($this->debug) echo "\n<br>-Captcha-Debug: The counterfilename is (".$this->tempfolder.$this->counter_filename.')';

			// retrieve last counter-value
			$test = $this->txt_counter($this->tempfolder.$this->counter_filename);

			// set and retrieve current counter-value
			$counter = $this->txt_counter($this->tempfolder.$this->counter_filename,TRUE);

			// check if counter works correct
			if(($counter !== FALSE) && ($counter - $test == 1))
			{
				// Counter works perfect, =:)
				if($this->debug) echo "\n<br>-Captcha-Debug: Current counter-value is ($counter). Garbage-collector should start at (".$this->collect_garbage_after.')';

				// check if garbage-collector should run
				if($counter >= $this->collect_garbage_after)
				{
					// Reset counter
					if($this->debug) echo "\n<br>-Captcha-Debug: Reset the counter-value. (0)";
					$this->txt_counter($this->tempfolder.$this->counter_filename,TRUE,0);

					// start garbage-collector
					$this->garbage_collector_error = $this->collect_garbage() ? FALSE : TRUE;
					if($this->debug) echo "\n<br>-Captcha-Debug: ERROR! SOME TRASHFILES COULD NOT BE DELETED!";
				}

			}
			else
			{
				// Counter-ERROR!
				if($this->debug) echo "\n<br>-Captcha-Debug: ERROR! NO COUNTER-VALUE AVAILABLE!";
			}


		}



	////////////////////////////////
	//
	//	PUBLIC METHODS
	//

		/**
		  * Generates a captcha image and returns the complete path to the image
		  *
		  **/
		function get_b2evo_captcha()
		{
			$this->make_captcha();
			if(!isset($public) || $public=='') $public = $this->public_key;
			return str_replace($_SERVER['DOCUMENT_ROOT'],'',$this->tempfolder).$this->filename_prefix.$public.'.jpg';
		}


		/**
		  *
		  * Validates submission and returns result
		  * Returns 0 = invalid sumbit | 1 = valid submit
		  *
		  **/
		function validate_submit($image,$attempt)
		{
			$correct_hash = substr($image,-36,32);
			if($this->case_sensitive==0) $attempt = strtoupper($attempt);
			if($this->check_captcha($correct_hash,$attempt))
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (1)";
				return 1;
			}
			else
			{
				if($this->debug) echo "\n<br>-Captcha-Debug: Validating submitted form returns: (0)";
				return 0;
			}
		}



	////////////////////////////////
	//
	//	PRIVATE METHODS
	//

		/** @private **/
		function make_captcha($private_key='')
		{
			if($private_key=='') $private_key = $this->generate_keypair();
	
			// set dimension of image
			$this->lx = (strlen($private_key) + 1) * (int)(($this->maxsize + $this->minsize) / 1.5);
			$this->ly = (int)(2.4 * $this->maxsize);
			if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Set image dimension to: (".$this->lx.' x '.$this->ly.')';
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate private key: ($private_key)";

			// set number of noise-chars for background if is enabled
			$this->nb_noise = $this->noise ? (strlen($private_key) * $this->noisefactor) : 0;
			if($this->debug) echo "\n<br>-b2evo-Captcha-Debug: Set number of noise characters to: (".$this->nb_noise.')';

			// create Image and set the apropriate function depending on GD-Version & websafecolor-value
			if($this->gd_version >= 2 && !$this->websafecolors)
			{
				$func1 = 'imagecreatetruecolor';
				$func2 = 'imagecolorallocate';
			}
			else
			{
				$func1 = 'imageCreate';
				$func2 = 'imagecolorclosest';
			}
			$image = $func1($this->lx,$this->ly);
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate ImageStream with: ($func1())";
			if($this->debug) echo "\n<br>-Captcha-Debug: For colordefinitions we use: ($func2())";


			// Set Backgroundcolor
			$this->random_color(224, 255);
			$back =  @imagecolorallocate($image, $this->r, $this->g, $this->b);
			@ImageFilledRectangle($image,0,0,$this->lx,$this->ly,$back);
			if($this->debug) echo "\n<br>-Captcha-Debug: We allocate one color for Background: (".$this->r.'-'.$this->g.'-'.$this->b.')';

			// allocates the 216 websafe color palette to the image
			if($this->gd_version < 2 || $this->websafecolors) $this->makeWebsafeColors($image);


			// fill with noise or grid
			if($this->nb_noise > 0)
			{
				// random characters in background with random position, angle, color
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with noise: (".$this->nb_noise.')';
				for($i=0; $i < $this->nb_noise; $i++)
				{
					$size	= intval(mt_rand((int)($this->minsize / 2.3), (int)($this->maxsize / 1.7)));
					$angle	= intval(mt_rand(0, 360));
					$x		= intval(mt_rand(0, $this->lx));
					$y		= intval(mt_rand(0, (int)($this->ly - ($size / 5))));
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					$text	= chr(intval(mt_rand(45,250)));
					@ImageTTFText($image, $size, $angle, $x, $y, $color, $this->change_TTF(), $text);
				}
			}
			else
			{
				// generate grid
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with x-gridlines: (".(int)($this->lx / (int)($this->minsize / 1.5)).')';
				for($i=0; $i < $this->lx; $i += (int)($this->minsize / 1.5))
				{
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					@imageline($image, $i, 0, $i, $this->ly, $color);
				}
				if($this->debug) echo "\n<br>-Captcha-Debug: Fill background with y-gridlines: (".(int)($this->ly / (int)(($this->minsize / 1.8))).')';
				for($i=0 ; $i < $this->ly; $i += (int)($this->minsize / 1.8))
				{
					$this->random_color(160, 224);
					$color	= $func2($image, $this->r, $this->g, $this->b);
					@imageline($image, 0, $i, $this->lx, $i, $color);
				}
			}

			// generate Text
			if($this->debug) echo "\n<br>-Captcha-Debug: Fill forground with chars and shadows: (".$this->chars.')';
			for($i=0, $x = intval(mt_rand($this->minsize,$this->maxsize)); $i < strlen($private_key); $i++)
			{
				$text	= substr($private_key, $i, 1);
				$angle	= intval(mt_rand(($this->maxrotation * -1), $this->maxrotation));
				$size	= intval(mt_rand($this->minsize, $this->maxsize));
				$y		= intval(mt_rand((int)($size * 1.5), (int)($this->ly - ($size / 7))));
				$this->random_color(0, 127);
				$color	=  $func2($image, $this->r, $this->g, $this->b);
				$this->random_color(0, 127);
				$shadow = $func2($image, $this->r + 127, $this->g + 127, $this->b + 127);
				@ImageTTFText($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $this->change_TTF(), $text);
				@ImageTTFText($image, $size, $angle, $x, $y - (int)($size / 15), $color, $this->TTF_file, $text);
				$x += (int)($size + ($this->minsize / 5));
			}
			@ImageJPEG($image, $this->get_filename(), $this->jpegquality);
			$res = file_exists($this->get_filename());
			if($this->debug) echo "\n<br>-Captcha-Debug: Save Image with quality [".$this->jpegquality.'] as ('.$this->get_filename().') returns: ('.($res ? 'TRUE' : 'FALSE').')';
			@ImageDestroy($image);
			if($this->debug) echo "\n<br>-Captcha-Debug: Destroy Imagestream.";
			if(!$res) die('Unable to save captcha-image.');
		}

		/** @private **/
		function makeWebsafeColors(&$image)
		{
			//$a = array();
			for($r = 0; $r <= 255; $r += 51)
			{
				for($g = 0; $g <= 255; $g += 51)
				{
					for($b = 0; $b <= 255; $b += 51)
					{
						$color = imagecolorallocate($image, $r, $g, $b);
						//$a[$color] = array('r'=>$r,'g'=>$g,'b'=>$b);
					}
				}
			}
			if($this->debug) echo "\n<br>-Captcha-Debug: Allocate 216 websafe colors to image: (".imagecolorstotal($image).')';
			//return $a;
		}

		/** @private **/
		function random_color($min,$max)
		{
			$this->r = intval(mt_rand($min,$max));
			$this->g = intval(mt_rand($min,$max));
			$this->b = intval(mt_rand($min,$max));
			//echo ' ('.$this->r.'-'.$this->g.'-'.$this->b.') ';
		}

		/** @private **/
		function change_TTF()
		{
			if(is_array($this->TTF_RANGE))
			{
				$key = array_rand($this->TTF_RANGE);
				$this->TTF_file = $this->TTF_folder.$this->TTF_RANGE[$key];
			}
			else
			{
				$this->TTF_file = $this->TTF_folder.$this->TTF_RANGE;
			}
			return $this->TTF_file;
		}

		/** @private **/
		function check_captcha($correct_hash,$attempt)
		{
			// when check, destroy picture on disk
			if(file_exists($this->get_filename($correct_hash)))
			{
				$res = @unlink($this->get_filename($correct_hash)) ? 'TRUE' : 'FALSE';
				if($this->debug) echo "\n<br>-Captcha-Debug: Delete image (".$this->get_filename($correct_hash).") returns: ($res)";
			}
			else
				return FALSE;

			$res = (md5($attempt)===$correct_hash) ? 'TRUE' : 'FALSE';
			if($this->debug) echo "\n<br>-Captcha-Debug: Comparing public with private key returns: ($res)";
			return $res == 'TRUE' ? TRUE : FALSE;
		}

		/** @private **/
		function get_filename($public='')
		{
			if($public=='') $public=$this->public_key;
			return $this->tempfolder.$this->filename_prefix.$public.'.jpg';
		}

		/** @private **/
		function get_filename_url($public="")
		{
			if($public=='') $public = $this->public_key;
			return str_replace($_SERVER['DOCUMENT_ROOT'],'',$this->tempfolder).$this->filename_prefix.$public.'.jpg';
		}

		/** @private **/
		function get_gd_version()
		{
			if (!function_exists('imagejpeg')) {
			   $gd_version_number = 0;
			} else {
				static $gd_version_number = null;
				if($gd_version_number === null)
				{
				   ob_start();
				   phpinfo(8);
				   $module_info = ob_get_contents();
				   ob_end_clean();
				   if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info, $matches))
				   {
					   $gd_version_number = $matches[1];
				   }
				   else
				   {
					   $gd_version_number = 0;
				   }
				}
			}
			return $gd_version_number;
		}


		// this is where the actual text and public hash is generated and stored
		function generate_keypair()
		{
			$key = '';
			$this->chars = mt_rand($this->minchars,$this->maxchars);
			for($i=0; $i < $this->chars; $i++) {
				$key .= $this->validchars{mt_rand(1,strlen($this->validchars))-1};
			}
			if($this->case_sensitive==0) $key = strtoupper($key);
			$this->public_key = md5($key);
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate Keys, private key is: (".$key.')';
			if($this->debug) echo "\n<br>-Captcha-Debug: Generate Keys, public key is: (".$this->public_key.')';
			return $key;
		}

		//Store/Retrieve a counter-value in/from a textfile. Optionally count it up or store a (as third param) specified value.
		// Returns counter-value
		function txt_counter($filename,$add=FALSE,$fixvalue=FALSE)
		{
			if(is_file($filename) ? TRUE : touch($filename))
			{
				if(is_readable($filename) && is_writable($filename))
				{
					$fp = @fopen($filename, 'r');
					if($fp)
					{
						$counter = (int)trim(fgets($fp));
						fclose($fp);

						if($add)
						{
							if($fixvalue !== FALSE)
							{
								$counter = (int)$fixvalue;
							}
							else
							{
								$counter++;
							}
							$fp = @fopen($filename, 'w');
							if($fp)
							{
								fputs($fp,$counter);
								fclose($fp);
								return $counter;
							}
							else return FALSE;
						}
						else
						{
							return $counter;
						}
					}
					else return FALSE;
				}
				else return FALSE;
			}
			else return FALSE;
		}

		// Scanns the tempfolder for jpeg-files with nameprefix used by the class and trash them if they are older than maxlifetime.
		function collect_garbage()
		{
			$OK = FALSE;
			$captchas = 0;
			$trashed = 0;
			if($handle = @opendir($this->tempfolder))
			{
				$OK = TRUE;
				while(false !== ($file = readdir($handle)))
				{
					if(!is_file($this->tempfolder.$file)) continue;
					// check for name-prefix, extension and filetime
					if(substr($file,0,strlen($this->prefix)) == $this->prefix)
					{
						if(strrchr($file, '.') == '.jpg')
						{
							$captchas++;
							if((time() - filemtime($this->tempfolder.$file)) >= $this->maxlifetime)
							{
								$trashed++;
								$res = @unlink($this->tempfolder.$file);
								if(!$res) $OK = FALSE;
							}
						}
					}
				}
				closedir($handle);
			}
			if($this->debug) echo "\n<br>-Captcha-Debug: There are ($captchas) captcha-images in tempfolder, where ($trashed) are seems to be lost.";
			return $OK;
		}

} // END CLASS b2evo_captcha
