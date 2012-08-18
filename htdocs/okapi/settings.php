<?

namespace okapi;

use Exception;
use okapi\Locales;

# DO NOT MODIFY THIS FILE. This file should always look like the original here:
# http://code.google.com/p/opencaching-api/source/browse/trunk/okapi/settings.php
#
# HOW TO MODIFY OKAPI SETTINGS: If you want a setting X to have a value of Y,
# add following lines to your OC's lib/settings.inc.php file:
#
#     $OKAPI_SETTINGS = array(
#         'X' => 'Y',
#         // ... etc ...
#     );
#
#     // E.g. $OKAPI_SETTINGS = array('OC_BRANCH' => 'oc.de', 'SITELANG' => 'de');
#
# This file provides documentation and DEFAULT values for those settings.
#
# Please note: These settings WILL mutate. Some of them might get deprecated,
# others might change their meaning and/or possible values.

final class Settings
{
	/** Default values for setting keys. */
	private static $DEFAULT_SETTINGS = array(
	
		/**
		 * Currently there are two mainstream branches of OpenCaching code.
		 * Which branch is you installation using?
		 * 
		 * Possible values: "oc.pl" or "oc.de". (As far as we know, oc.us and
		 * oc.org.uk use "oc.pl" branch, the rest uses "oc.de" branch.)
		 */
		'OC_BRANCH' => "oc.pl",
		
		/**
		 * Each OpenCaching site has a default language. I.e. the language in
		 * which all the names of caches are entered. What is the ISO 639-1 code
		 * of this language? Note: ISO 639-1 codes are always lowercase.
		 * 
		 * E.g. "pl", "en", "de".
		 */
		'SITELANG' => "en",
		
		/**
		 * All OKAPI documentation pages should remain English-only, but some
		 * other pages (and results) might be translated to their localized
		 * versions. We try to catch up to all OKAPI instances and
		 * fill our default translation tables with all the languages of all
		 * OKAPI installations. But we also give you an option to use your own
		 * translation table if you really need to. Use this variable to pass your
		 * own gettext initialization function/method. See default_gettext_init
		 * function below for details.
		 */
		'GETTEXT_INIT' => array('\okapi\Settings', 'default_gettext_init'),
		
		/**
		 * By default, OKAPI uses "okapi_messages" domain file for translations.
		 * Use this variable when you want it to use your own domain.
		 */
		'GETTEXT_DOMAIN' => 'okapi_messages',
		
		/**
		 * By default, OKAPI sends messages to email address defined in $GLOBALS['sql_errormail'].
		 * However, there can be only one address defined there. If you want to add more, you may
		 * use this setting to provide a list of additional email addresses.
		 */
		'EXTRA_ADMINS' => array(),
		
		/**
		 * Where should OKAPI store dynamically generated cache files? If you leave it at null,
		 * OKAPI will try to guess (not recommended). If you move this directory, it's better
		 * if you also move all the files which were inside.
		 */
		'VAR_DIR' => null,
		
		/**
		 * Set to true, if your installation supports "Needs maintenance" log type (with
		 * log type id == 5). If your users are not allowed to submit "Needs maintenance"
		 * log entries, leave it at false.
		 */
		'SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE' => false,
		
		/**
		 * Set to true, to prevent OKAPI from sending email messages. ALLOWED ONLY ON
		 * DEVELOPMENT ENVIRONMENT! Sending emails is vital for OKAPI administration and
		 * usage! (I.e. users need this to receive their tokens upon registration.)
		 */
		'DEBUG_PREVENT_EMAILS' => false,
		
		/**
		 * Set to true, to prevent OKAPI from using sem_get family of functions.
		 * ALLOWED ONLY ON DEVELOPMENT ENVIRONMENT! Semaphores are vital for OKAPI's
		 * performance and data integrity!
		 */
		'DEBUG_PREVENT_SEMAPHORES' => false,
	);
	
	/** 
	 * Final values for settings keys (defaults + local overrides).
	 * (Loaded upon first access.)
	 */
	private static $SETTINGS = null;
	
	/**
	 * Initialize self::$SETTINGS.
	 */
	private static function load_settings()
	{
		# Check the settings.inc.php for overrides.
		
		self::$SETTINGS = self::$DEFAULT_SETTINGS;
		
		$ref = null;
		if (isset($GLOBALS['OKAPI_SETTINGS']))
		{
			$ref = &$GLOBALS['OKAPI_SETTINGS'];
		}
		else
		{
			throw new Exception("Could not locate OKAPI settings! Put your settings array in ".
				"\$GLOBALS['OKAPI_SETTINGS']]. Settings may be empty, but must exist.");
		}

		foreach (self::$SETTINGS as $key => $_)
		{
			if (isset($ref[$key]))
			{
				self::$SETTINGS[$key] = $ref[$key];
				self::verify($key, self::$SETTINGS[$key]);
			}
		}
	}
	
	/** Throw an exception, if given $value is invalid for the given $key. */
	private static function verify($key, $value)
	{
		$debug = (isset($GLOBALS['debug_page']) && $GLOBALS['debug_page']);
		if (($key == 'OC_BRANCH') && (!in_array($value, array('oc.pl', 'oc.de'))))
			throw new Exception("Currently, OC_BRANCH has to be either 'oc.pl' or 'oc.de'. Hint: Whom did you get your code from?");
		$boolean_keys = array('SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE', 'DEBUG_PREVENT_EMAILS', 'DEBUG_PREVENT_SEMAPHORES');
		if (in_array($key, $boolean_keys) && (!in_array($value, array(true, false))))
			throw new Exception("Invalid value for $key.");
		if (($key == 'DEBUG_PREVENT_EMAILS') and ($value == true) and ($debug == false))
			throw new Exception("DEBUG_PREVENT_EMAILS might be used only when debugging. Sending emails is vital in production environment.");
		if (($key == 'DEBUG_PREVENT_SEMAPHORES') and ($value == true) and ($debug == false))
			throw new Exception("USE_SEMAPHORES might be used only when debugging. Semaphores are vital in production environment.");
	}
	
	/** 
	 * Get the value for the $key setting.
	 */
	public static function get($key)
	{
		if (self::$SETTINGS == null)
			self::load_settings();
		
		if (!array_key_exists($key, self::$SETTINGS))
			throw new Exception("Tried to access an invalid settings key: '$key'");
		
		return self::$SETTINGS[$key];
	}
	
	/**
	 * Bind "okapi_messages" with our local i18n database. Set proper locale
	 * based on the language codes passed and return the locale code.
	 * $langprefs is a list of language codes in order of preference.
	 * 
	 * Please note, that OKAPI consumers may ask OKAPI to return contents
	 * in a specified language. (For example, consumers from Germany may ask
	 * Polish OKAPI server to return GPX file in German.) If you insist on using
	 * your own translation tables, you should still fallback to the default
	 * OKAPI translations table in case of other languages!
	 */
	public static function default_gettext_init($langprefs)
	{
		require_once 'locale/locales.php';
		$locale = Locales::get_best_locale($langprefs);
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		setlocale(LC_NUMERIC, "POSIX"); # We don't want *this one* to get out of control.
		bindtextdomain("okapi_messages", $GLOBALS['rootpath'].'okapi/locale');
		return $locale;
	}
	
	public static function describe_settings()
	{
		return print_r(self::$SETTINGS, true);
	}
}