<?php

namespace okapi;

use Exception;
use okapi\core\Okapi;
use okapi\core\OkapiErrorHandler;
use okapi\locale\Locales;

# DO NOT MODIFY THIS FILE. This file should always look like the original here:
# https://github.com/opencaching/okapi/blob/master/okapi/settings.php
#
# HOW TO MODIFY OKAPI SETTINGS: If you want a setting X to have a value of Y,
# create/edit the "<rootpath>/okapi_settings.php" file. See example here:
# https://github.com/opencaching/opencaching-pl/blob/master/okapi_settings.php
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
         * List of administrator email addresses. OKAPI will send important messages
         * to this addresses. You should replace this with your true email address.
         */
        'ADMINS' => array(),

        /** Set this to true on development machines. */
        'DEBUG' => false,

        /**
         * Currently there are two mainstream branches of Opencaching code.
         * Which branch is you installation using?
         *
         * Possible values: "oc.pl" or "oc.de". (As far as we know, oc.us and
         * oc.org.uk use "oc.pl" branch, the rest uses "oc.de" branch.)
         */
        'OC_BRANCH' => "oc.pl",

        /**
         * Each Opencaching site has a default language. I.e. the language in
         * which all the names of caches are entered. What is the ISO 639-1 code
         * of this language? Note: ISO 639-1 codes are always lowercase.
         *
         * E.g. "pl", "en", "de".
         */
        'SITELANG' => "en",

        /**
         * If set, it will be passed to date_default_timezone_set.
         *
         * If your system is configured properly, then you are not required to
         * set this value. However, in other cases, OKAPI may refuse to start
         * if this value is not set (more information here:
         * https://github.com/opencaching/okapi/issues/177).
         *
         * You should set it to the timezone used in your country (the one you'd
         * use for your database inserts etc.). Choose one of the values listed
         * here: https://www.php.net/manual/en/timezones.php
         *
         * E.g. "Europe/Berlin", "America/New_York".
         */
        'TIMEZONE' => null,

        /** Email address to use in the "From:" when sending messages. */
        'FROM_FIELD' => 'root@localhost',

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
         * Where should OKAPI store dynamically generated cache files? If you leave it at null,
         * OKAPI will try to guess (not recommended). If you move this directory, it's better
         * if you also move all the files which were inside.
         */
        'VAR_DIR' => null,

        /**
         * Where to store uploaded images? This directory needs to be shared among
         * both OKAPI and OC code (see $picdir in your settings.inc.php).
         */
        'IMAGES_DIR' => null,

        /**
         * The URL that corresponds to the IMAGES_DIR.
         */
        'IMAGES_URL' => null,

        /**
         * Name of the cookie within which OC stores serialized session id, etc.
         * OKAPI requires to access this in order to make sure which user is logged
         * in.
         */
        'OC_COOKIE_NAME' => null,

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

        /* Database settings */

        'DB_SERVER' => 'localhost',
        'DB_NAME' => null,
        'DB_USERNAME' => null,
        'DB_PASSWORD' => null,
        'DB_CHARSET' => 'utf8',

        /**
         * Canonical URL of this Opencaching site (with a trailing slash). If
         * you prefer your site to be accessed via HTTPS, then this should be
         * a "https://" URL.
         *
         * If this is a development installation, it should point to the local
         * URL (e.g. "http://localhost/").
         */
        'SITE_URL' => null,

        /**
         * Deprecated. For details, see here:
         * https://github.com/opencaching/okapi/issues/417
         */
        'ORIGIN_URL' => null,

        /**
         * Node ID. This is the value OKAPI should put into `node_id` columns
         * in OC tables when it inserts new rows into tables with this column.
         */
        'OC_NODE_ID' => null,

        /**
         * Your OC sites data licencing document. All OKAPI Consumers will be
         * required to accept this.
         */
        'DATA_LICENSE_URL' => null,

        /**
         * URLs for registering a new user account at the OC site
         */
        'REGISTRATION_URL' => null,
        'MOBILE_REGISTRATION_URL' => null,

        /**
         * URL of the site's logo image.
         * Minimum size is 64x64 pixels, maximum size 72x72 pixels.
         */
        'SITE_LOGO' => null,

        /**
         * Settings for the OCDE HTML purifier which is used by services/logs/submit.
         */
        'OCDE_HTML_PURIFIER_SETTINGS' => array(),

        /**
         * This is a BETA feature, and it works on OCPL branch only (it
         * requires some undocumented OCPL tables). This feature may be removed
         * at any moment. OCPL admins temporarily use feature to track
         * suspicious activity of some certain users.
         */
        'OCPL_ENABLE_GEOCACHE_ACCESS_LOGS' => false,

        /**
         * GitHub access token. If given, it will allow OKAPI to make more
         * frequent queries to the GitHub API (e.g. in the services/apiref/issue
         * method). Providing this is not required, but it is highly recommended.
         *
         * Get it here (we don't need any scopes):
         * https://help.github.com/articles/creating-an-access-token-for-command-line-use/
         */
        'GITHUB_ACCESS_TOKEN' => null,

        /**
         * Maximum size of uploaded images in bytes
         */
        'IMAGE_MAX_UPLOAD_SIZE' => 4194304,  # 4 MB

        /**
         * Maximum 'resolution' of saved images in pixels
         */
        'IMAGE_MAX_PIXEL_COUNT' => 524288,  # 0.5 MP

        /**
         * Quality of saved JPEG images on a scale from 50 to 100. The higher the
         * quality, the sharper the image and larger the saved image files
         * (file size grows exponentially). The PHP default is 75, which is also
         * used in OCPL code. As OKAPI always resamples uploaded images - which
         * involves a bit of quality loss - we set a higher default:
         */
        'JPEG_QUALITY' => 80,

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
        try {
            # This is an external code and it MAY generate E_NOTICEs.
            # We have to temporarily disable our default error handler.

            OkapiErrorHandler::disable();
            require_once __DIR__ . '/../okapi_settings.php';
            $ref = get_okapi_settings();
            OkapiErrorHandler::reenable();

        } catch (Exception $e) {
            throw new Exception("Could not import <rootpath>/okapi_settings.php:\n".$e->getMessage());
        }
        self::$SETTINGS = self::$DEFAULT_SETTINGS;
        foreach (self::$SETTINGS as $key => $_)
        {
            if (isset($ref[$key]))
            {
                self::$SETTINGS[$key] = $ref[$key];
            }
        }
        self::verify(self::$SETTINGS);
    }

    private static function verify(&$dict)
    {
        if (!in_array($dict['OC_BRANCH'], array('oc.pl', 'oc.de')))
            throw new Exception("Currently, OC_BRANCH has to be either 'oc.pl' or 'oc.de'. Hint: Whom did you get your code from?");
        $boolean_keys = array('DEBUG', 'DEBUG_PREVENT_EMAILS', 'DEBUG_PREVENT_SEMAPHORES');
        foreach ($boolean_keys as $key)
            if (!in_array($dict[$key], array(true, false)))
                throw new Exception("Invalid value for $key.");
        if (count($dict['ADMINS']) == 0)
            throw new Exception("ADMINS array has to filled (e.g. array('root@localhost')).");
        if ($dict['DEBUG'] == false)
            foreach ($dict as $k => $v)
                if ((strpos($k, 'DEBUG_') === 0) && $v == true)
                    throw new Exception("When DEBUG is false, $k has to be false too.");
        if ($dict['VAR_DIR'] == null)
            throw new Exception("VAR_DIR cannot be null. Please provide a valid directory.");
        if ($dict['IMAGES_DIR'] == null)
            throw new Exception("IMAGES_DIR cannot be null. Please provide a valid directory.");
        foreach ($dict as $k => $v)
            if ((strpos($k, '_DIR') !== false) && ($k[strlen($k) - 1] == '/'))
                throw new Exception("None of the *_DIR settings may end with a slash. Check $k.");
        $notnull = array(
            'OC_COOKIE_NAME', 'DB_SERVER', 'DB_NAME', 'DB_USERNAME', 'SITE_URL',
            'IMAGES_URL', 'OC_NODE_ID');
        foreach ($notnull as $k)
            if ($dict[$k] === null)
                throw new Exception("$k cannot be null.");
        $slash_keys = array('SITE_URL', 'IMAGES_URL');
        foreach ($slash_keys as $key)
            if ($dict[$key][strlen($dict[$key]) - 1] != '/')
                throw new Exception("$key must end with a slash.");
        if ($dict['REGISTRATION_URL'] === null)
            $dict['REGISTRATION_URL'] = $dict['SITE_URL'] . 'register.php';
        if ($dict['SITE_LOGO'] === null)
            $dict['SITE_LOGO'] = $dict['SITE_URL'] . 'okapi/static/oc_logo.png';
        if ($dict['JPEG_QUALITY'] < 50 || $dict['JPEG_QUALITY'] > 100)
            throw new Exception('JPEG_QUALITY must be between 50 and 100.');

        # The OKAPI code is only compatible with utf8 and utf8mb4 charsets.
        if (!in_array($dict['DB_CHARSET'], array('utf8', 'utf8mb4'))) {
            throw new exception("DB_CHARSET must be utf8 or utf8mb4.");
        }
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
        $locale = Locales::get_best_locale($langprefs);
        putenv("LC_ALL=$locale");
        setlocale(LC_ALL, $locale);
        setlocale(LC_NUMERIC, "POSIX"); # We don't want *this one* to get out of control.
        bindtextdomain("okapi_messages", $GLOBALS['rootpath'].'okapi/locale');
        return $locale;
    }

    public static function describe_settings()
    {
        return Okapi::removeSensitiveData(print_r(self::$SETTINGS, true));
    }
}
