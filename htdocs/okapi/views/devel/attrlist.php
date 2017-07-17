<?php

namespace okapi\views\devel\attrlist;

use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\services\attrs\AttrHelper;
use okapi\Settings;

class View
{
    public static function call()
    {
        # This is a hidden page for OKAPI developers. It will list all
        # attributes defined in this OC installation (and some other stuff).

        ob_start();

        print "Cache Types:\n\n";
        foreach (self::get_all_cachetypes() as $id => $name)
            print "$id: $name\n";

        print "\nLog Types:\n\n";
        foreach (self::get_all_logtypes() as $id => $name)
            print "$id: $name\n";

        print "\nAttributes:\n\n";
        $internal2acode = AttrHelper::get_internal_id_to_acode_mapping();
        $dict = self::get_all_attribute_names();
        foreach ($dict as $internal_id => $langs)
        {
            print $internal_id.": ";
            $langkeys = array_keys($langs);
            sort($langkeys);
            if (in_array('en', $langkeys))
                print strtoupper($langs['en']);
            else
                print ">>>> ENGLISH NAME UNSET! <<<<";
            if (isset($internal2acode[$internal_id]))
                print " - ".$internal2acode[$internal_id];
            else
                print " - >>>> MISSING A-CODE MAPPING <<<<";
            print "\n";
            foreach ($langkeys as $langkey)
                print "        $langkey: ".$langs[$langkey]."\n";
        }

        print "\nAttribute notices:\n\n";
        print "There are three priorities: (!), (-) and ( )\n";
        print "(the last one ( ) can be safely ignored)\n\n";

        $attrdict = AttrHelper::get_attrdict();
        foreach ($dict as $internal_id => $langs)
        {
            if (!isset($internal2acode[$internal_id]))
            {
                print "(!) Attribute ".$internal_id." is not mapped to any A-code.\n";
                continue;
            }
            $acode = $internal2acode[$internal_id];
            $attr = $attrdict[$acode];
            foreach ($langs as $lang => $value)
            {
                if ($lang == 'en')
                {
                    continue;
                }
                if (!isset($attr['names'][$lang]))
                {
                    print "(-) Attribute $acode is missing a name in the '$lang' language.\n";
                    print "    Local name: $value\n";
                    print "    OKAPI name: >> none <<\n";
                    continue;
                }
                if ($attr['names'][$lang] !== $value)
                {
                    print "( ) Attribute $acode has a different name in the '$lang' language\n";
                    print "    Local name: $value\n";
                    print "    OKAPI name: ".$attr['names'][$lang]."\n";
                }
            }
        }

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        $response->body = ob_get_clean();
        return $response;
    }

    /**
     * Get an array of all site-specific attributes in the following format:
     * $arr[<id_of_the_attribute>][<language_code>] = <attribute_name>.
     */
    private static function get_all_attribute_names()
    {
        if (Settings::get('OC_BRANCH') == 'oc.pl')
        {
            # OCPL branch uses cache_attrib table to store attribute names. It has
            # different structure than the OCDE cache_attrib table. OCPL does not
            # have translation tables.

            $rs = Db::query("select id, language, text_long from cache_attrib order by id");
        }
        else
        {
            # OCDE branch uses translation tables. Let's make a select which will
            # produce results compatible with the one above.

            $rs = Db::query("
                select
                    ca.id,
                    stt.lang as language,
                    stt.text as text_long
                from
                    cache_attrib ca,
                    sys_trans_text stt
                where ca.trans_id = stt.trans_id
                order by ca.id
            ");
        }

        $dict = array();
        while ($row = Db::fetch_assoc($rs)) {
            $dict[$row['id']][strtolower($row['language'])] = $row['text_long'];
        }
        return $dict;
    }

    /**
     * Get an array of all site-specific cache-types (id => name in English).
     */
    private static function get_all_cachetypes()
    {
        if (Settings::get('OC_BRANCH') == 'oc.pl')
        {
            # OCPL branch does not store cache types in many languages (just two).

            $rs = Db::query("select id, en from cache_type order by id");
        }
        else
        {
            # OCDE branch uses translation tables.

            $rs = Db::query("
                select
                    ct.id,
                    stt.text as en
                from
                    cache_type ct
                    left join sys_trans_text stt
                        on ct.trans_id = stt.trans_id
                        and stt.lang = 'EN'
                order by ct.id
            ");
        }

        $dict = array();
        while ($row = Db::fetch_assoc($rs)) {
            $dict[$row['id']] = $row['en'];
        }
        return $dict;
    }

    /**
     * Get an array of all site-specific log-types (id => name in English).
     */
    private static function get_all_logtypes()
    {
        if (Settings::get('OC_BRANCH') == 'oc.pl')
        {
            # OCPL branch does not store cache types in many languages (just two).

            $rs = Db::query("select id, en from log_types order by id");
        }
        else
        {
            # OCDE branch uses translation tables.

            $rs = Db::query("
                select
                    lt.id,
                    stt.text as en
                from
                    log_types lt
                    left join sys_trans_text stt
                        on lt.trans_id = stt.trans_id
                        and stt.lang = 'EN'
                order by lt.id
            ");
        }

        $dict = array();
        while ($row = Db::fetch_assoc($rs)) {
            $dict[$row['id']] = $row['en'];
        }
        return $dict;
    }
}
