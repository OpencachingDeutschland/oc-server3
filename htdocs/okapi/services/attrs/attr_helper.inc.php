<?php

namespace okapi\services\attrs;

use Exception;
use ErrorException;
use okapi\Okapi;
use okapi\Settings;
use okapi\Cache;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\OkapiLock;
use SimpleXMLElement;


class AttrHelper
{
    /**
     * By default, when DEBUG mode is enabled, the attributes.xml file is
     * reloaded practically on every request. If you don't want that, you can
     * temporarilly disable this behavior by settings this to false.
     */
    private static $RELOAD_ON_DEBUG = true;

    private static $attr_dict = null;

    /**
     * Return the cache key suffix to be used for caching. This should be used
     * In order for the $RELOAD_ON_DEBUG to work properly when switching to/from
     * DEBUG mode.
     */
    private static function cache_key_suffix()
    {
        return (self::$RELOAD_ON_DEBUG) ? "#DBG" : "";
    }

    /** Return the timeout to be used for attribute caching. */
    private static function ttl()
    {
        return (Settings::get('DEBUG') && self::$RELOAD_ON_DEBUG) ? 2 : 86400;
    }

    /**
     * Forces an immediate refresh of the current attributes from the
     * attribute-definitions.xml file.
     */
    public static function refresh_now()
    {
        try
        {
            $path = $GLOBALS['rootpath']."okapi/services/attrs/attribute-definitions.xml";
            $xml = file_get_contents($path);
            self::refresh_from_string($xml);
        }
        catch (Exception $e)
        {
            # Failed to read or parse the file (i.e. after a syntax error was
            # commited). Let's check when the last successful parse occured.

            self::init_from_cache(false);

            if (self::$attr_dict === null)
            {
                # That's bad! We don't have ANY copy of the data AND we failed
                # to parse it. We will use a fake, empty data.

                $cache_key = "attrhelper/dict#".Okapi::$revision.self::cache_key_suffix();
                $cachedvalue = array(
                    'attr_dict' => array(),
                );
                Cache::set($cache_key, $cachedvalue, self::ttl());
            }

            return;
        }
    }

    /**
     * Refresh all attributes from the given XML. Usually, this file is
     * downloaded from Google Code (using refresh_now).
     */
    public static function refresh_from_string($xml)
    {
        /* The attribute-definitions.xml file defines relationships between
         * attributes originating from various OC installations. Each
         * installation uses internal IDs of its own. Which "attribute schema"
         * is being used in THIS installation? */

        $my_schema = Settings::get('ORIGIN_URL');

        $doc = simplexml_load_string($xml);
        $cachedvalue = array(
            'attr_dict' => array(),
        );

        # Build cache attributes dictionary

        $all_internal_ids = array();
        foreach ($doc->attr as $attrnode)
        {
            $attr = array(
                'acode' => (string)$attrnode['acode'],
                'gc_equivs' => array(),
                'internal_id' => null,
                'names' => array(),
                'descriptions' => array(),
                'is_discontinued' => true
            );
            foreach ($attrnode->groundspeak as $gsnode)
            {
                $attr['gc_equivs'][] = array(
                    'id' => (int)$gsnode['id'],
                    'inc' => in_array((string)$gsnode['inc'], array("true", "1")) ? 1 : 0,
                    'name' => (string)$gsnode['name']
                );
            }
            foreach ($attrnode->opencaching as $ocnode)
            {
                /* If it is used by at least one OC node, then it's NOT discontinued. */
                $attr['is_discontinued'] = false;

                if ((string)$ocnode['schema'] == $my_schema)
                {
                    /* It is used by THIS OC node. */

                    $internal_id = (int)$ocnode['id'];
                    if (isset($all_internal_ids[$internal_id]))
                        throw new Exception("The internal attribute ".$internal_id.
                            " has multiple assigments to OKAPI attributes.");
                    $all_internal_ids[$internal_id] = true;
                    if (!is_null($attr['internal_id']))
                        throw new Exception("There are multiple internal IDs for the ".
                            $attr['acode']." attribute.");
                    $attr['internal_id'] = $internal_id;
                }
            }
            foreach ($attrnode->lang as $langnode)
            {
                $lang = (string)$langnode['id'];
                foreach ($langnode->name as $namenode)
                {
                    if (isset($attr['names'][$lang]))
                        throw new Exception("Duplicate ".$lang." name of attribute ".$attr['acode']);
                    $attr['names'][$lang] = (string)$namenode;
                }
                foreach ($langnode->desc as $descnode)
                {
                    if (isset($attr['descriptions'][$lang]))
                        throw new Exception("Duplicate ".$lang." description of attribute ".$attr['acode']);
                    $xml = $descnode->asxml(); /* contains "<desc>" and "</desc>" */
                    $innerxml = preg_replace("/(^[^>]+>)|(<[^<]+$)/us", "", $xml);
                    $attr['descriptions'][$lang] = self::cleanup_string($innerxml);
                }
            }
            $cachedvalue['attr_dict'][$attr['acode']] = $attr;
        }

        $cache_key = "attrhelper/dict#".Okapi::$revision.self::cache_key_suffix();
        Cache::set($cache_key, $cachedvalue, self::ttl());
        self::$attr_dict = $cachedvalue['attr_dict'];
    }

    /**
     * Object to be used for forward-compatibility (see the attributes method).
     */
    public static function get_unknown_placeholder($acode)
    {
        return array(
            'acode' => $acode,
            'gc_equivs' => array(),
            'internal_id' => null,
            'names' => array(
                'en' => "Unknown attribute"
            ),
            'descriptions' => array(
                'en' => (
                    "This attribute ($acode) is unknown at ".Okapi::get_normalized_site_name().
                    ". It might not exist, or it may be a new attribute, recognized ".
                    "only in newer OKAPI installations. Perhaps ".Okapi::get_normalized_site_name().
                    " needs to have its OKAPI updated?"
                )
            ),
            'is_discontinued' => true
        );
    }

    /**
     * Initialize all the internal attributes (if not yet initialized). This
     * loads attribute values from the cache. If they are not present in the
     * cache, it will read and parse them from attribute-definitions.xml file.
     */
    private static function init_from_cache($allow_refreshing=true)
    {
        if (self::$attr_dict !== null)
        {
            /* Already initialized. */
            return;
        }
        $cache_key = "attrhelper/dict#".Okapi::$revision.self::cache_key_suffix();
        $cachedvalue = Cache::get($cache_key);
        if ($cachedvalue === null)
        {
            # I.e. after Okapi::$revision is changed, or cache got invalidated.

            if ($allow_refreshing)
            {
                self::refresh_now();
                self::init_from_cache(false);
                return;
            }
            else
            {
                $cachedvalue = array(
                    'attr_dict' => array(),
                );
            }
        }
        self::$attr_dict = $cachedvalue['attr_dict'];
    }

    /**
     * Return a dictionary of all attributes. The format is INTERNAL and PRIVATE,
     * it is NOT the same as in the "attributes" method (but it is quite similar).
     */
    public static function get_attrdict()
    {
        self::init_from_cache();
        return self::$attr_dict;
    }

    /** "\n\t\tBla   blabla\n\t\t<b>bla</b>bla.\n\t" => "Bla blabla <b>bla</b>bla." */
    private static function cleanup_string($s)
    {
        return preg_replace('/(^\s+)|(\s+$)/us', "", preg_replace('/\s+/us', " ", $s));
    }

    /**
     * Get the mapping table between internal attribute id => OKAPI A-code.
     * The result is cached!
     */
    public static function get_internal_id_to_acode_mapping()
    {
        static $mapping = null;
        if ($mapping !== null)
            return $mapping;

        $cache_key = "attrhelper/id2acode/".Okapi::$revision.self::cache_key_suffix();
        $mapping = Cache::get($cache_key);
        if (!$mapping)
        {
            self::init_from_cache();
            $mapping = array();
            foreach (self::$attr_dict as $acode => &$attr_ref)
                $mapping[$attr_ref['internal_id']] = $acode;
            Cache::set($cache_key, $mapping, self::ttl());
        }
        return $mapping;
    }

    /**
     * Get the mapping: A-codes => attribute name. The language for the name
     * is selected based on the $langpref parameter. The result is cached!
     */
    public static function get_acode_to_name_mapping($langpref)
    {
        static $mapping = null;
        if ($mapping !== null)
            return $mapping;

        $cache_key = md5(serialize(array("attrhelper/acode2name", $langpref,
            Okapi::$revision, self::cache_key_suffix())));
        $mapping = Cache::get($cache_key);
        if (!$mapping)
        {
            self::init_from_cache();
            $mapping = array();
            foreach (self::$attr_dict as $acode => &$attr_ref)
            {
                $mapping[$acode] = Okapi::pick_best_language(
                    $attr_ref['names'], $langpref);
            }
            Cache::set($cache_key, $mapping, self::ttl());
        }
        return $mapping;
    }
}
