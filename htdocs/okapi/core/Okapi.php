<?php

namespace okapi\core;

use okapi\core\Consumer\OkapiConsumer;
use okapi\core\CronJob\CronJobController;
use okapi\core\Exception\BadRequest;
use okapi\core\Exception\DbException;
use okapi\core\Exception\InvalidParam;
use okapi\core\Exception\JobsAlreadyInProgress;
use okapi\core\Exception\OkapiExceptionHandler;
use okapi\core\OAuth\OkapiOAuthServer;
use okapi\core\Request\OkapiInternalRequest;
use okapi\core\Request\OkapiRequest;
use okapi\core\Response\OkapiHttpResponse;
use okapi\Settings;

/** Container for various OKAPI functions. */
class Okapi
{
    public static $data_store;
    public static $server;

    /* These two get replaced in automatically deployed packages. */
    public static $version_number = 1632;
    public static $git_revision = '429b57d0232fda93382abe0cee7409eeb17d390f';

    private static $okapi_vars = null;

    /** Return a new, random UUID. */
    public static function create_uuid()
    {
        /* If we're on Linux, then we'll use a system function for that. */

        if (file_exists("/proc/sys/kernel/random/uuid")) {
            return trim(file_get_contents("/proc/sys/kernel/random/uuid"));
        }

        /* On other systems (as well as on some other Linux distributions)
         * fall back to the original implementation (which is NOT safe - we had
         * one duplicate during 3 years of its running). */

        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /** Get a variable stored in okapi_vars. If variable not found, return $default. */
    public static function get_var($varname, $default = null)
    {
        if (self::$okapi_vars === null)
        {
            $rs = Db::query("
                select var, value
                from okapi_vars
            ");
            self::$okapi_vars = array();
            while ($row = Db::fetch_assoc($rs))
                self::$okapi_vars[$row['var']] = $row['value'];
        }
        if (isset(self::$okapi_vars[$varname]))
            return self::$okapi_vars[$varname];
        return $default;
    }

    /**
     * Save a variable to okapi_vars. WARNING: The entire content of okapi_vars table
     * is loaded on EVERY execution. Do not store data in this table, unless it's
     * frequently needed.
     */
    public static function set_var($varname, $value)
    {
        Okapi::get_var($varname);
        Db::execute("
            replace into okapi_vars (var, value)
            values (
                '".Db::escape_string($varname)."',
                '".Db::escape_string($value)."');
        ");
        self::$okapi_vars[$varname] = $value;
    }

    /**
     * Remove database passwords and other sensitive data from the given
     * message (which is usually a trace, var_dump or print_r output).
     */
    public static function removeSensitiveData($message)
    {
        # This method is initially defined in the OkapiExceptionHandler class,
        # so that it is accessible even before the Okapi class is initialized.

        return OkapiExceptionHandler::removeSensitiveData($message);
    }

    /** Send an email message to local OKAPI administrators. */
    public static function mail_admins($subject, $message)
    {
        # Make sure we're not sending HUGE emails.

        if (strlen($message) > 100000) {
            $message = substr($message, 0, 100000)."\n\n...(message clipped at 100k chars)\n";
        }

        # Make sure we're not spamming.

        $cache_key = 'mail_admins_counter/'.(floor(time() / 3600) * 3600).'/'.md5($subject);
        try {
            $counter = Cache::get($cache_key);
        } catch (\Exception $e) {
            # Exception can occur during OKAPI update (#156, #434), or when
            # the cache table is broken (#340). I am not sure which option is
            # better: 1. notify the admins about the error and risk spamming
            # them, 2. don't notify and don't risk spamming them. Currently,
            # I choose option 2.

            return;
        }
        if ($counter === null)
            $counter = 0;
        $counter++;
        try {
            Cache::set($cache_key, $counter, 3600);
        } catch (DbException $e) {
            # If `get` succeeded and `set` did not, then probably we're having
            # issue #156 scenario. We can ignore it here.
        }
        if ($counter <= 5)
        {
            # We're not spamming yet.

            self::mail_from_okapi(\get_admin_emails(), $subject, $message);
        }
        else
        {
            # We are spamming. Prevent sending more emails.

            $content_cache_key_prefix = 'mail_admins_spam/'.(floor(time() / 3600) * 3600).'/';
            $timeout = 86400;
            if ($counter == 6)
            {
                self::mail_from_okapi(\get_admin_emails(), "Anti-spam mode activated for '$subject'",
                    "OKAPI has activated an \"anti-spam\" mode for the following subject:\n\n".
                    "\"$subject\"\n\n".
                    "Anti-spam mode is activiated when more than 5 messages with\n".
                    "the same subject are sent within one hour.\n\n".
                    "Additional debug information:\n".
                    "- counter cache key: $cache_key\n".
                    "- content prefix: $content_cache_key_prefix<n>\n".
                    "- content timeout: $timeout\n"
                );
            }
            $content_cache_key = $content_cache_key_prefix.$counter;
            Cache::set($content_cache_key, $message, $timeout);
        }
    }

    /** Send an email message from OKAPI to the given recipients. */
    public static function mail_from_okapi($email_addresses, $subject, $message)
    {
        if (class_exists("okapi\\Settings") && (Settings::get('DEBUG_PREVENT_EMAILS')))
        {
            # Sending emails was blocked on admin's demand.
            # This is possible only on development environment.
            return;
        }
        if (!is_array($email_addresses))
            $email_addresses = array($email_addresses);
        $sender_email = class_exists("okapi\\Settings") ? Settings::get('FROM_FIELD') : 'root@localhost';
        mail(implode(", ", $email_addresses), $subject, $message,
            "Content-Type: text/plain; charset=utf-8\n".
            "From: OKAPI <$sender_email>\n".
            "Reply-To: $sender_email\n"
        );
    }

    /** Get directory to store dynamic (cache or temporary) files. No trailing slash included. */
    public static function get_var_dir()
    {
        $dir = Settings::get('VAR_DIR');
        if ($dir != null)
            return rtrim($dir, "/");
        throw new \Exception("You need to set a valid VAR_DIR.");
    }

    /** Returns something like "Opencaching.PL" or "Opencaching.DE". */
    public static function get_normalized_site_name($site_url = null)
    {
        if ($site_url == null)
            $site_url = Settings::get('SITE_URL');
        $matches = null;
        if (preg_match("#^https?://(www.)?opencaching.([a-z.]+)/$#", $site_url, $matches)) {
            return "Opencaching.".strtoupper($matches[2]);
        }
        if (preg_match("#^https?://(www.)?opencache.([a-z.]+)/$#", $site_url, $matches)) {
            return "Opencache.".strtoupper($matches[2]);
        }

        return "DEVELSITE";
    }

    /**
     * Return a "code" of this OC node.
     *
     * These values are used internally only, they SHOULD NOT be exposed to
     * external developers!
     */
    public static function get_oc_installation_code()
    {
        if (Settings::get('OC_BRANCH') == 'oc.de') {
            return "OCDE";  // OC
        }
        $mapping = array(
            2 => "OCPL",  // OP
            6 => "OCUK",  // OK
            10 => "OCUS",  // OU
            14 => "OCNL",  // OB
            16 => "OCRO",  // OR
            // should be expanded when new OCPL-based sites are added
        );
        $oc_node_id = Settings::get("OC_NODE_ID");
        if (isset($mapping[$oc_node_id])) {
            return $mapping[$oc_node_id];
        }

        return "OTHER";
    }

    /**
     * Return the recommended okapi_base_url.
     *
     * This is the URL which we want all *new* client applications to use.
     * OKAPI will suggest URLs with this prefix in various context, e.g. in all
     * the dynamically generated docs.
     *
     * Also see `get_allowed_base_urls` method.
     */
    public static function get_recommended_base_url()
    {
        return Settings::get('SITE_URL')."okapi/";
    }

    /**
     * Return a list of okapi_base_urls allowed to be used when calling OKAPI
     * methods in this installation.
     *
     * Since issue #416, the "recommended" okapi_base_url is *not* the only one
     * allowed (actually, there were more allowed before issue #416, but they
     * weren't allowed "officially").
     */
    public static function get_allowed_base_urls()
    {
        /* Currently, there are no config settings which would let us allow
         * to determine the proper values for this list. So, we need to have it
         * hardcoded. (Perhaps we should move this to etc/installations.xml?
         * But this wouldn't be efficient...) */

        switch (self::get_oc_installation_code()) {
            case 'OCPL':
                $urls = array(
                    "http://opencaching.pl/okapi/",
                    "http://www.opencaching.pl/okapi/",
                    "https://opencaching.pl/okapi/",
                );
                break;
            case 'OCDE':
                if (in_array(Settings::get('OC_NODE_ID'), array(4,5))) {
                    /* In OCDE, node_ids 4 and 5 are used to indicate a development
                     * installation. Other sites rely on the fact, that
                     * self::get_recommended_base_url() is appended to $urls below.
                     * For OCDE this is not enough, because they want to test both
                     * HTTP and HTTPS in their development installations. */
                    $urls = array(
                        preg_replace("/^https:/", "http:", Settings::get('SITE_URL')) . 'okapi/',
                        preg_replace("/^http:/", "https:", Settings::get('SITE_URL')) . 'okapi/',
                    );
                } else {
                    $urls = array(
                        "http://www.opencaching.de/okapi/",
                        "https://www.opencaching.de/okapi/",
                    );
                }
                break;
            case 'OCNL':
                $urls = array(
                    "http://www.opencaching.nl/okapi/",
                );
                break;
            case 'OCRO':
                $urls = array(
                    "http://www.opencaching.ro/okapi/",
                );
                break;
            case 'OCUK':
                $urls = array(
                    "http://opencache.uk/okapi/",
                    "https://opencache.uk/okapi/",
                );
                break;
            case 'OCUS':
                $urls = array(
                    "http://www.opencaching.us/okapi/",
                    "http://opencaching.us/okapi/",
                );
                break;
            default:
                /* Unknown site. No extra allowed URLs. */
                $urls = array();
        }

        if (!in_array(self::get_recommended_base_url(), $urls)) {
            $urls[] = self::get_recommended_base_url();
        }

        return $urls;
    }

    /**
     * Pick text from $langdict based on language preference $langpref.
     *
     * Example:
     * pick_best_language(
     *   array('pl' => 'X', 'de' => 'Y', 'en' => 'Z'),
     *   array('sp', 'de', 'en')
     * ) == 'Y'.
     *
     * @param array $langdict - assoc array of lang-code => text.
     * @param array $langprefs - list of lang codes, in order of preference.
     */
    public static function pick_best_language($langdict, $langprefs)
    {
        /* Try langprefs first. */
        foreach ($langprefs as $pref) {
            if (isset($langdict[$pref])) {
                return $langdict[$pref];
            }
        }

        /* Try English next. */
        if (isset($langdict['en'])) {
            return $langdict['en'];
        }

        /* Then, try SITELANG. Should be filled in most cases. */
        if (isset($langdict[Settings::get('SITELANG')])) {
            return $langdict[Settings::get('SITELANG')];
        }

        /* Finally, just pick any. */
        foreach ($langdict as &$text_ref)
            return $text_ref;

        /* Langdict is empty. Simply return an empty string. */
        return "";
    }

    /**
     * Split the array into groups of max. $size items.
     */
    public static function make_groups($array, $size)
    {
        $i = 0;
        $groups = array();
        while ($i < count($array))
        {
            $groups[] = array_slice($array, $i, $size);
            $i += $size;
        }
        return $groups;
    }

    /**
     * Check if any pre-request cronjobs are scheduled to execute and execute
     * them if needed. Reschedule for new executions.
     */
    public static function execute_prerequest_cronjobs()
    {
        $nearest_event = Okapi::get_var("cron_nearest_event");
        if ($nearest_event + 0 <= time())
        {
            try {
                $nearest_event = CronJobController::run_jobs('pre-request');
                Okapi::set_var("cron_nearest_event", $nearest_event);
            } catch (JobsAlreadyInProgress $e) {
                // Ignore.
            }
        }
    }

    /**
     * Check if any cron-5 cronjobs are scheduled to execute and execute
     * them if needed. Reschedule for new executions.
     *
     * If other thread is currently handling the jobs, then do nothing.
     */
    public static function execute_cron5_cronjobs()
    {
        $nearest_event = Okapi::get_var("cron_nearest_event");
        if ($nearest_event + 0 <= time())
        {
            set_time_limit(0);
            ignore_user_abort(true);
            try {
                $nearest_event = CronJobController::run_jobs('cron-5');
                Okapi::set_var("cron_nearest_event", $nearest_event);
            } catch (JobsAlreadyInProgress $e) {
                // Ignore.
            }
        }
    }

    private static function gettext_set_lang($langprefs)
    {
        static $gettext_last_used_langprefs = null;
        static $gettext_last_set_locale = null;

        # We remember the last $langprefs argument which we've been called with.
        # This way, we don't need to call the actual locale-switching code most
        # of the times.

        if ($gettext_last_used_langprefs != $langprefs)
        {
            $gettext_last_set_locale = call_user_func(Settings::get("GETTEXT_INIT"), $langprefs);
            $gettext_last_used_langprefs = $langprefs;
            textdomain(Settings::get("GETTEXT_DOMAIN"));
        }
        return $gettext_last_set_locale;
    }

    private static $gettext_original_domain = null;
    private static $gettext_langprefs_stack = array();

    /**
     * Attempt to switch the language based on the preference array given.
     * Previous language settings will be remembered (in a stack). You SHOULD
     * restore them later by calling gettext_domain_restore.
     */
    public static function gettext_domain_init($langprefs = null)
    {
        # Put the langprefs on the stack.

        if ($langprefs == null)
            $langprefs = array(Settings::get('SITELANG'));
        self::$gettext_langprefs_stack[] = $langprefs;

        if (count(self::$gettext_langprefs_stack) == 1)
        {
            # This is the first time gettext_domain_init is called. In order to
            # properly reinitialize the original settings after gettext_domain_restore
            # is called for the last time, we need to save current textdomain (which
            # should be different than the one which we use - Settings::get("GETTEXT_DOMAIN")).

            self::$gettext_original_domain = textdomain(null);
        }

        # Attempt to change the language. Acquire the actual locale code used
        # (might differ from expected when language was not found).

        return self::gettext_set_lang($langprefs);
    }
    public static function gettext_domain_restore()
    {
        # Dismiss the last element on the langpref stack. This is the language
        # which we've been actualy using until now. We want it replaced with
        # the language below it.

        array_pop(self::$gettext_langprefs_stack);

        $size = count(self::$gettext_langprefs_stack);
        if ($size > 0)
        {
            $langprefs = self::$gettext_langprefs_stack[$size - 1];
            self::gettext_set_lang($langprefs);
        }
        else
        {
            # The stack is empty. This means we're going out of OKAPI code and
            # we want the original textdomain reestablished.

            textdomain(self::$gettext_original_domain);
            self::$gettext_original_domain = null;
        }
    }

    /**
     * Internal. This is called always when OKAPI core is included.
     */
    public static function init_internals($allow_cronjobs = true)
    {
        static $init_made = false;
        if ($init_made)
            return;
        self::increase_memory_limit('512M');
        # The memory limit is - among other - crucial for the maximum size
        # of processable images; see services/logs/images/add.php: max_pixels()
        Db::connect();
        if (Settings::get('TIMEZONE') !== null)
            date_default_timezone_set(Settings::get('TIMEZONE'));
        if (!self::$data_store)
            self::$data_store = new OkapiDataStore();
        if (!self::$server)
            self::$server = new OkapiOAuthServer(self::$data_store);
        if ($allow_cronjobs)
            self::execute_prerequest_cronjobs();
        $init_made = true;
    }

    /**
     * Increase memory limit (only if $value is larger than the current memory
     * limit).
     */
    public static function increase_memory_limit($value)
    {
        $current = self::from_human_to_bytes(ini_get('memory_limit'));
        $new = self::from_human_to_bytes($value);
        if ($current < $new) {
            ini_set('memory_limit', $value);
        }
    }

    /**
     * Generate a string of random characters, suitable for keys as passwords.
     * Troublesome characters like '0', 'O', '1', 'l' will not be used.
     * If $user_friendly=true, then it will consist from numbers only.
     */
    public static function generate_key($length, $user_friendly = false)
    {
        if ($user_friendly)
            $chars = "0123456789";
        else
            $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        $max = strlen($chars);
        $key = "";
        for ($i=0; $i<$length; $i++)
        {
            $key .= $chars[rand(0, $max-1)];
        }
        return $key;
    }

    /**
     * Register new OKAPI Consumer, send him an email with his key-pair, etc.
     * This method does not verify parameter values, check if they are in
     * a correct format prior the execution.
     */
    public static function register_new_consumer($appname, $appurl, $email)
    {
        $consumer = new OkapiConsumer(Okapi::generate_key(20), Okapi::generate_key(40),
            $appname, $appurl, $email);
        $sample_cache = OkapiServiceRunner::call("services/caches/search/all",
            new OkapiInternalRequest($consumer, null, array('limit', 1)));
        if (count($sample_cache['results']) > 0)
            $sample_cache_code = $sample_cache['results'][0];
        else
            $sample_cache_code = "CACHECODE";

        # Message for the Consumer.
        ob_start();
        print "This is the key-pair we have created for your application:\n\n";
        print "Consumer Key: $consumer->key\n";
        print "Consumer Secret: $consumer->secret\n\n";
        print "Note: Consumer Secret is needed only when you intend to use OAuth.\n";
        print "You don't need Consumer Secret for Level 1 Authentication.\n\n";
        print "Now you can easily access Level 1 OKAPI methods. E.g.:\n";
        print Settings::get('SITE_URL')."okapi/services/caches/geocache?cache_code=$sample_cache_code&consumer_key=$consumer->key\n\n";
        print "If you plan on using OKAPI for a longer time, then you might also want\n";
        print "to subscribe to the OKAPI News blog (it is not much updated, but it might\n";
        print "still be worth the trouble):\n";
        print "https://opencaching-api.blogspot.com/\n\n";
        print "Have fun!\n\n";
        print "-- \n";
        print "OKAPI Team\n";
        Okapi::mail_from_okapi($email, "Your OKAPI Consumer Key", ob_get_clean());

        # Message for the Admins.

        ob_start();
        print "Name: $consumer->name\n";
        print "Developer: $consumer->email\n";
        print ($consumer->url ? "URL: $consumer->url\n" : "");
        print "Consumer Key: $consumer->key\n";
        Okapi::mail_admins("New OKAPI app registered!", ob_get_clean());

        Db::execute("
            insert into okapi_consumers (`key`, name, secret, url, email, date_created)
            values (
                '".Db::escape_string($consumer->key)."',
                '".Db::escape_string($consumer->name)."',
                '".Db::escape_string($consumer->secret)."',
                '".Db::escape_string($consumer->url)."',
                '".Db::escape_string($consumer->email)."',
                now()
            );
        ");
    }

    /** Return the distance between two geopoints, in meters. */
    public static function get_distance($lat1, $lon1, $lat2, $lon2)
    {
        $x1 = (90-$lat1) * 3.14159 / 180;
        $x2 = (90-$lat2) * 3.14159 / 180;
        #
        # min(1, ...) was added below to prevent getting values greater than 1 due
        # to floating point precision limits. See issue #351 for details.
        #
        $d = acos(min(1, cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180))) * 6371000;
        if ($d < 0) $d = 0;
        return $d;
    }

    /**
     * Return an SQL formula for calculating distance between two geopoints.
     * Parameters should be either numberals or strings (SQL field references).
     */
    public static function get_distance_sql($lat1, $lon1, $lat2, $lon2)
    {
        $x1 = "(90-$lat1) * 3.14159 / 180";
        $x2 = "(90-$lat2) * 3.14159 / 180";
        #
        # least(1, ...) was added below to prevent getting values greater than 1 due
        # to floating point precision limits. See issue #351 for details.
        #
        $d = "acos(least(1, cos($x1) * cos($x2) + sin($x1) * sin($x2) * cos(($lon1-$lon2) * 3.14159 / 180))) * 6371000";
        return $d;
    }

    /** Return bearing (float 0..360) from geopoint 1 to 2. */
    public static function get_bearing($lat1, $lon1, $lat2, $lon2)
    {
        if ($lat1 == $lat2 && $lon1 == $lon2)
            return null;
        if ($lat1 == $lat2) $lat1 += 0.0000166;
        if ($lon1 == $lon2) $lon1 += 0.0000166;

        $rad_lat1 = $lat1 / 180.0 * 3.14159;
        $rad_lon1 = $lon1 / 180.0 * 3.14159;
        $rad_lat2 = $lat2 / 180.0 * 3.14159;
        $rad_lon2 = $lon2 / 180.0 * 3.14159;

        $delta_lon = $rad_lon2 - $rad_lon1;
        $bearing = atan2(sin($delta_lon) * cos($rad_lat2),
            cos($rad_lat1) * sin($rad_lat2) - sin($rad_lat1) * cos($rad_lat2) * cos($delta_lon));
        $bearing = 180.0 * $bearing / 3.14159;
        if ( $bearing < 0.0 ) $bearing = $bearing + 360.0;

        return $bearing;
    }

    /** Transform bearing (float 0..360) to simple 2-letter string (N, NE, E, SE, etc.) */
    public static function bearing_as_two_letters($b)
    {
        static $names = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW');
        if ($b === null) return 'n/a';
        return $names[round(($b / 360.0) * 8.0) % 8];
    }

    /** Transform bearing (float 0..360) to simple 3-letter string (N, NNE, NE, ESE, etc.) */
    public static function bearing_as_three_letters($b)
    {
        static $names = array('N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW');
        if ($b === null) return 'n/a';
        return $names[round(($b / 360.0) * 16.0) % 16];
    }

    /** Escape string for use with XML. See issue 169. */
    public static function xmlescape($string)
    {
        static $pattern = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';
        $string = preg_replace($pattern, '', $string);
        return strtr($string, array("<" => "&lt;", ">" => "&gt;", "\"" => "&quot;", "'" => "&apos;", "&" => "&amp;"));
    }

    /**
     * Return object as a standard OKAPI response. The $object will be formatted
     * using one of the default formatters (JSON, JSONP, XML, etc.). Formatter is
     * auto-detected by peeking on the $request's 'format' parameter. In some
     * specific cases, this method can also return the $object itself, instead
     * of OkapiResponse - this allows nesting methods within other methods.
     */
    public static function formatted_response(OkapiRequest $request, &$object)
    {
        if ($request instanceof OkapiInternalRequest && (!$request->i_want_OkapiResponse))
        {
            # If you call a method internally, then you probably expect to get
            # the actual object instead of it's formatted representation.
            return $object;
        }
        $format = $request->get_parameter('format');
        if ($format == null) $format = 'json';
        if (!in_array($format, array('json', 'jsonp', 'xmlmap', 'xmlmap2')))
            throw new InvalidParam('format', "'$format'");
        $callback = $request->get_parameter('callback');
        if ($callback && $format != 'jsonp')
            throw new BadRequest("The 'callback' parameter is reserved to be used with the JSONP output format.");
        if ($format == 'json')
        {
            $response = new OkapiHttpResponse();
            $response->content_type = "application/json; charset=utf-8";
            $response->body = json_encode($object);
            return $response;
        }
        elseif ($format == 'jsonp')
        {
            if (!$callback)
                throw new BadRequest("'callback' parameter is required for JSONP calls");
            if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $callback))
                throw new InvalidParam('callback', "'$callback' doesn't seem to be a valid JavaScript function name (should match /^[a-zA-Z_][a-zA-Z0-9_]*\$/).");
            $response = new OkapiHttpResponse();
            $response->content_type = "application/javascript; charset=utf-8";
            $response->body = $callback."(".json_encode($object).");";
            return $response;
        }
        elseif ($format == 'xmlmap')
        {
            # Deprecated (see issue 128). Keeping this for backward-compatibility.
            $response = new OkapiHttpResponse();
            $response->content_type = "text/xml; charset=utf-8";
            $response->body = self::xmlmap_dumps($object);
            return $response;
        }
        elseif ($format == 'xmlmap2')
        {
            $response = new OkapiHttpResponse();
            $response->content_type = "text/xml; charset=utf-8";
            $response->body = self::xmlmap2_dumps($object);
            return $response;
        }
        else
        {
            # Should not happen (as we do a proper check above).
            throw new \Exception();
        }
    }

    private static function _xmlmap_add(&$chunks, &$obj)
    {
        if (is_string($obj))
        {
            $chunks[] = "<string>";
            $chunks[] = self::xmlescape($obj);
            $chunks[] = "</string>";
        }
        elseif (is_int($obj))
        {
            $chunks[] = "<int>$obj</int>";
        }
        elseif (is_float($obj))
        {
            $chunks[] = "<float>$obj</float>";
        }
        elseif (is_bool($obj))
        {
            $chunks[] = $obj ? "<bool>true</bool>" : "<bool>false</bool>";
        }
        elseif (is_null($obj))
        {
            $chunks[] = "<null/>";
        }
        elseif (is_array($obj))
        {
            # Have to check if this is associative or not! Shit. I hate PHP.
            if (array_keys($obj) === range(0, count($obj) - 1))
            {
                # Not assoc.
                $chunks[] = "<list>";
                foreach ($obj as &$item_ref)
                {
                    $chunks[] = "<item>";
                    self::_xmlmap_add($chunks, $item_ref);
                    $chunks[] = "</item>";
                }
                $chunks[] = "</list>";
            }
            else
            {
                # Assoc.
                $chunks[] = "<dict>";
                foreach ($obj as $key => &$item_ref)
                {
                    $chunks[] = "<item key=\"".self::xmlescape($key)."\">";
                    self::_xmlmap_add($chunks, $item_ref);
                    $chunks[] = "</item>";
                }
                $chunks[] = "</dict>";
            }
        }
        else
        {
            # That's a bug.
            throw new \Exception("Cannot encode as xmlmap: " . print_r($obj, true));
        }
    }

    private static function _xmlmap2_add(&$chunks, &$obj, $key)
    {
        $attrs = ($key !== null) ? " key=\"".self::xmlescape($key)."\"" : "";
        if (is_string($obj))
        {
            $chunks[] = "<string$attrs>";
            $chunks[] = self::xmlescape($obj);
            $chunks[] = "</string>";
        }
        elseif (is_int($obj))
        {
            $chunks[] = "<number$attrs>$obj</number>";
        }
        elseif (is_float($obj))
        {
            $chunks[] = "<number$attrs>$obj</number>";
        }
        elseif (is_bool($obj))
        {
            $chunks[] = $obj ? "<boolean$attrs>true</boolean>" : "<boolean$attrs>false</boolean>";
        }
        elseif (is_null($obj))
        {
            $chunks[] = "<null$attrs/>";
        }
        elseif (is_array($obj) || ($obj instanceof \ArrayObject))
        {
            # Have to check if this is associative or not! Shit. I hate PHP.
            if (is_array($obj) && (array_keys($obj) === range(0, count($obj) - 1)))
            {
                # Not assoc.
                $chunks[] = "<array$attrs>";
                foreach ($obj as &$item_ref)
                {
                    self::_xmlmap2_add($chunks, $item_ref, null);
                }
                $chunks[] = "</array>";
            }
            else
            {
                # Assoc.
                $chunks[] = "<object$attrs>";
                foreach ($obj as $key => &$item_ref)
                {
                    self::_xmlmap2_add($chunks, $item_ref, $key);
                }
                $chunks[] = "</object>";
            }
        }
        else
        {
            # That's a bug.
            throw new \Exception("Cannot encode as xmlmap2: " . print_r($obj, true));
        }
    }

    /** Return the object in a serialized version, in the (deprecated) "xmlmap" format. */
    public static function xmlmap_dumps(&$obj)
    {
        $chunks = array();
        self::_xmlmap_add($chunks, $obj);
        return implode('', $chunks);
    }

    /** Return the object in a serialized version, in the "xmlmap2" format. */
    public static function xmlmap2_dumps(&$obj)
    {
        $chunks = array();
        self::_xmlmap2_add($chunks, $obj, null);
        return implode('', $chunks);
    }

    private static $cache_types = array(
        #
        # OKAPI does not expose type IDs. Instead, it uses the following
        # "code words". Only the "primary" cache types are documented.
        # This means that all other types may (in theory) be altered.
        # Cache type may become "primary" ONLY when *all* OC servers recognize
        # that type.
        #
        # Changing this may introduce nasty bugs (e.g. in the replicate module).
        # CONTACT ME BEFORE YOU MODIFY THIS!
        #
        'oc.pl' => array(
            # Primary types (documented, cannot change)
            'Traditional' => 2, 'Multi' => 3, 'Quiz' => 7, 'Virtual' => 4,
            'Event' => 6,
            # Additional types (may get changed)
            'Other' => 1, 'Webcam' => 5,
            'Moving' => 8, 'Podcast' => 9, 'Own' => 10,
        ),
        'oc.de' => array(
            # Primary types (documented, cannot change)
            'Traditional' => 2, 'Multi' => 3, 'Quiz' => 7, 'Virtual' => 4,
            'Event' => 6,
            # Additional types (might get changed)
            'Other' => 1, 'Webcam' => 5,
            'Math/Physics' => 8, 'Moving' => 9, 'Drive-In' => 10,
        )
    );

    /** E.g. 'Traditional' => 2. For unknown names throw an Exception. */
    public static function cache_type_name2id($name)
    {
        $ref = &self::$cache_types[Settings::get('OC_BRANCH')];
        if (isset($ref[$name]))
            return $ref[$name];
        throw new \Exception("Method cache_type_name2id called with unsupported cache ".
            "type name '$name'. You should not allow users to submit caches ".
            "of non-primary type.");
    }

    /** E.g. 2 => 'Traditional'. For unknown ids returns "Other". */
    public static function cache_type_id2name($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_types[Settings::get('OC_BRANCH')] as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return "Other";
    }

    private static $cache_statuses = array(
        'Available' => 1, 'Temporarily unavailable' => 2, 'Archived' => 3
    );

    /** E.g. 'Available' => 1. For unknown names throws an Exception. */
    public static function cache_status_name2id($name)
    {
        if (isset(self::$cache_statuses[$name]))
            return self::$cache_statuses[$name];
        throw new \Exception("Method cache_status_name2id called with invalid name '$name'.");
    }

    /** E.g. 1 => 'Available'. For unknown ids returns 'Archived'. */
    public static function cache_status_id2name($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_statuses as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return 'Archived';
    }

    private static $cache_sizes = array(
        'none' => 7,
        'nano' => 8,
        'micro' => 2,
        'small' => 3,
        'regular' => 4,
        'large' => 5,
        'xlarge' => 6,
        'other' => 1,
    );

    /** E.g. 'micro' => 2. For unknown names throw an Exception. */
    public static function cache_size2_to_sizeid($size2)
    {
        if (isset(self::$cache_sizes[$size2]))
            return self::$cache_sizes[$size2];
        throw new \Exception("Method cache_size2_to_sizeid called with invalid size2 '$size2'.");
    }

    /** E.g. 2 => 'micro'. For unknown ids returns "other". */
    public static function cache_sizeid_to_size2($id)
    {
        static $reversed = null;
        if ($reversed == null)
        {
            $reversed = array();
            foreach (self::$cache_sizes as $key => $value)
                $reversed[$value] = $key;
        }
        if (isset($reversed[$id]))
            return $reversed[$id];
        return "other";
    }

    /** Maps OKAPI's 'size2' values to opencaching.com (OX) size codes. */
    private static $cache_OX_sizes = array(
        'none' => null,
        'nano' => 1.3,
        'micro' => 2.0,
        'small' => 3.0,
        'regular' => 3.8,
        'large' => 4.6,
        'xlarge' => 4.9,
        'other' => null,
    );

    /**
     * E.g. 'micro' => 2.0, 'other' => null. For unknown names throw an
     * Exception. Note, that this is not a bijection ('none' are 'other' are
     * both null).
     */
    public static function cache_size2_to_oxsize($size2)
    {
        if (array_key_exists($size2, self::$cache_OX_sizes))
            return self::$cache_OX_sizes[$size2];
        throw new \Exception("Method cache_size2_to_oxsize called with invalid size2 '$size2'.");
    }

    /**
     * E.g. 'Found it' => 1. For unsupported names throws Exception.
     */
    public static function logtypename2id($name)
    {
        if ($name == 'Found it') return 1;
        if ($name == "Didn't find it") return 2;
        if ($name == 'Comment') return 3;
        if ($name == 'Attended') return 7;
        if ($name == 'Will attend') return 8;
        if (($name == 'Needs maintenance') && (Settings::get('OC_BRANCH') == 'oc.pl')) return 5;
        throw new \Exception("logtype2id called with invalid log type argument: $name");
    }

    /** E.g. 1 => 'Found it'. For unknown ids returns 'Comment'. */
    public static function logtypeid2name($id)
    {
        # Various OC sites use different English names, even for primary
        # log types. OKAPI needs to have them the same across *all* OKAPI
        # installations. That's why all known types are hardcoded here.
        # These names are officially documented and may never change!

        # Primary.
        if ($id == 1) return "Found it";
        if ($id == 2) return "Didn't find it";
        if ($id == 3) return "Comment";
        if ($id == 7) return "Attended";
        if ($id == 8) return "Will attend";

        # Other.
        if ($id == 4) return "Moved";
        if ($id == 5) return "Needs maintenance";
        if ($id == 6) return "Maintenance performed";
        if ($id == 9) return "Archived";
        if ($id == 10) return "Ready to search";
        if ($id == 11) return "Temporarily unavailable";
        if ($id == 12) return "OC Team comment";
        if ($id == 13 || $id == 14) return "Locked";

        # Important: This set is not closed. Other types may be introduced
        # in the future. This has to be documented in the public method
        # description.

        return "Comment";
    }

    /**
     * "Fix" user-supplied HTML fetched from the OC database.
     */
    public static function fix_oc_html($html, $object_type)
    {
        /* There are thousands of relative URLs in cache descriptions. We will
         * attempt to find them and fix them. In theory, the "proper" way to do this
         * would be to parse the description into a DOM tree, but that would simply
         * be very hard (and inefficient) to do, since most of the descriptions are
         * not even valid HTML.
         */

        $html = preg_replace(
            "~\b(src|href)=([\"'])(?![a-z0-9_-]+:)~",
            "$1=$2".Settings::get("SITE_URL"),
            $html
        );

        if ($object_type == self::OBJECT_TYPE_CACHE_LOG
            && Settings::get('OC_BRANCH') === 'oc.pl'
        ) {
            # Decode special OCPL entity-encoding produced by logs/submit.
            # See https://github.com/opencaching/okapi/issues/413.
            #
            # This might be restricted to log entries created by OKAPI (that are
            # recorded in okapi_submitted_objects). However, they may have been
            # edited later, so we don't know who created the HTML encoding.

            $html = preg_replace('/&amp;#(38|60|62);/', '&#$1;', $html);
        }

        /* Other things to do in the future:
         *
         * 1. Check for XSS vulnerabilities?
         * 2. Transform to a valid (X)HTML?
         */

        return $html;
    }

    /**
     * Convert strings such as "2M" or "50k" to bytes.
     */
    public static function from_human_to_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch($last) {
            case 'g':
                return substr($val, 0, strlen($val) - 1) * 1024 * 1024 * 1024;
            case 'm':
                return substr($val, 0, strlen($val) - 1) * 1024 * 1024;
            case 'k':
                return substr($val, 0, strlen($val) - 1) * 1024;
            default:
                if (($last < '0') || ($last > '9')) {
                    throw new \Exception("Unknown suffix");
                }

                return $val;
        }
    }

    /**
     * Some pages should be visible only to OKAPI developers (e.g. frequent
     * stats generation may reduce OKAPI responsiveness). This method verifies
     * that the requester is a developer. If he isn't, it die()s.
     */
    public static function require_developer_cookie() {
        if (
            (!isset($_COOKIE['okapi_devel_key']))
            || (md5($_COOKIE['okapi_devel_key']) != '5753f318c1495c01637f7f6b7fc9c5db')
        ) {
            header("Content-Type: text/plain; charset=utf-8");
            print "I need a cookie!";
            die();
        }
    }

    /**
     * Update the "last activity" field of the user. As explained in #337, it is stored
     * in `last_login` column and is needed for some reports. As explained in #439, it
     * shouldn't be updated automatically on each Level 3 request (because some of these
     * requests are not necessarilly initiated by the user).
     */
    public static function update_user_activity($request) {
        if ($request && $request->token && $request->token->token_type == "access") {
            Db::execute("
                update user set last_login=now()
                where user_id='".Db::escape_string($request->token->user_id)."'
            ");
        }
    }

    /**
     * Take a list of "infotags" (as defined in services/apiref/method), and format
     * them for being displayed in OKAPI public documentation pages.
     */
    public static function format_infotags($infotags) {
        $chunks = [];
        $url = Settings::get('SITE_URL')."okapi/introduction.html#oc-branch-differences";
        foreach ($infotags as $infotag) {
            if ($infotag == "ocpl-specific") {
                $chunks[] = "<a href='$url' class='infotag infotag-ocpl-specific'>OCPL</a> ";
            } elseif ($infotag == "ocde-specific") {
                $chunks[] = "<a href='$url' class='infotag infotag-ocde-specific'>OCDE</a> ";
            }
        }
        return implode("", $chunks);
    }

    # object types in table okapi_submitted_objects
    const OBJECT_TYPE_CACHE = 1;
    const OBJECT_TYPE_CACHE_DESCRIPTION = 2;
    const OBJECT_TYPE_CACHE_IMAGE = 3;
    const OBJECT_TYPE_CACHE_MP3 = 4;
    const OBJECT_TYPE_CACHE_LOG = 5;           # implemented
    const OBJECT_TYPE_CACHE_LOG_IMAGE = 6;     # implemented
    const OBJECT_TYPE_CACHELIST = 7;
    const OBJECT_TYPE_EMAIL = 8;
    const OBJECT_TYPE_CACHE_REPORT = 9;
}
