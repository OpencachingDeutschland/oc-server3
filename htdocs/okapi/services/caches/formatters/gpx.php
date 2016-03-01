<?php

namespace okapi\services\caches\formatters\gpx;

use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\OkapiAccessToken;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;
use okapi\OkapiInternalConsumer;
use okapi\Db;
use okapi\Settings;
use okapi\services\attrs\AttrHelper;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    /** Maps OKAPI cache type codes to Geocaching.com GPX cache types. */
    public static $cache_GPX_types = array(
        'Traditional' => 'Traditional Cache',
        'Multi' => 'Multi-Cache',
        'Quiz' => 'Unknown Cache',
        'Event' => 'Event Cache',
        'Virtual' => 'Virtual Cache',
        'Webcam' => 'Webcam Cache',
        'Moving' => 'Unknown Cache',
        'Math/Physics' => 'Unknown Cache',
        'Drive-In' => 'Traditional Cache',
        'Podcast' => 'Unknown Cache',
        'Own' => 'Unknown Cache',
        'Other' => 'Unknown Cache'
    );

    /** Maps OKAPI's 'size2' values to geocaching.com size codes. */
    public static $cache_GPX_sizes = array(
        'none' => 'Virtual',
        'nano' => 'Micro',
        'micro' => 'Micro',
        'small' => 'Small',
        'regular' => 'Regular',
        'large' => 'Large',
        'xlarge' => 'Large',
        'other' => 'Other',
    );

    /**
     * When used in create_gpx() method, enables GGZ index generation.
     * The index is then returned along the method's response, in the
     * 'ggz_entries' key. See formatters/ggz method.
     */
    const FLAG_CREATE_GGZ_IDX = 1;

    public static function call(OkapiRequest $request)
    {
        $response = new OkapiHttpResponse();
        $response->content_type = "application/gpx; charset=utf-8";
        $response->content_disposition = 'attachment; filename="results.gpx"';

        $result_ref = self::create_gpx($request);
        $response->body = &$result_ref['gpx'];

        return $response;
    }

    /**
     * Generate a GPX file.
     *
     * @param OkapiRequest $request
     * @param integer $flags
     * @throws BadRequest
     * @return An array with GPX file content under 'gpx' key
     */
    public static function create_gpx(OkapiRequest $request, $flags = null)
    {
        $vars = array();

        # Validating arguments. We will also assign some of them to the
        # $vars variable which we will use later in the GPS template.

        $cache_codes = $request->get_parameter('cache_codes');
        if ($cache_codes === null) throw new ParamMissing('cache_codes');

        # Issue 106 requires us to allow empty list of cache codes to be passed into this method.
        # All of the queries below have to be ready for $cache_codes to be empty!

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langpref .= "|".Settings::get('SITELANG');
        foreach (array('ns_ground', 'ns_gsak', 'ns_ox', 'latest_logs', 'alt_wpts', 'mark_found') as $param)
        {
            $val = $request->get_parameter($param);
            if (!$val) $val = "false";
            elseif (!in_array($val, array("true", "false")))
                throw new InvalidParam($param);
            $vars[$param] = ($val == "true");
        }
        if ($vars['latest_logs'] && (!$vars['ns_ground']))
            throw new BadRequest("In order for 'latest_logs' to work you have to also include 'ns_ground' extensions.");

        $tmp = $request->get_parameter('my_notes');
        $vars['my_notes'] = array();
        if ($tmp && $tmp != 'none') {
            $tmp = explode('|', $tmp);
            foreach ($tmp as $elem) {
                if ($elem == 'none') {
                    /* pass */
                } elseif (in_array($elem, array('desc:text', 'gc:personal_note'))) {
                    if (in_array('none', $tmp)) {
                        throw new InvalidParam(
                            'my_notes', "You cannot mix 'none' and '$elem'"
                        );
                    }
                    if ($request->token == null) {
                        throw new BadRequest(
                            "Level 3 Authentication is required to access my_notes data."
                        );
                    }
                    $vars['my_notes'][] = $elem;
                } else {
                    throw new InvalidParam('my_notes', "Invalid list entry: '$elem'");
                }
            }
        }

        $images = $request->get_parameter('images');
        if (!$images) $images = 'descrefs:nonspoilers';
        if (!in_array($images, array('none', 'descrefs:thumblinks', 'descrefs:nonspoilers', 'descrefs:all', 'ox:all')))
            throw new InvalidParam('images', "'$images'");
        $vars['images'] = $images;

        $tmp = $request->get_parameter('attrs');
        if (!$tmp) $tmp = 'desc:text';
        $tmp = explode("|", $tmp);
        $vars['attrs'] = array();
        foreach ($tmp as $elem)
        {
            if ($elem == 'none') {
                /* pass */
            } elseif (in_array($elem, array('desc:text', 'ox:tags', 'gc:attrs', 'gc_ocde:attrs'))) {
                if ($elem == 'gc_ocde:attrs' && Settings::get('OC_BRANCH') != 'oc.de')
                    $vars['attrs'][] = 'gc:attrs';
                else
                    $vars['attrs'][] = $elem;
            } else {
                throw new InvalidParam('attrs', "Invalid list entry: '$elem'");
            }
        }

        $protection_areas = $request->get_parameter('protection_areas');
        if (!$protection_areas || $protection_areas == 'desc:auto')
        {
            if (Settings::get('OC_BRANCH') == 'oc.de') $protection_areas = 'desc:text';
            else $protection_areas = 'none';
        }
        if (!in_array($protection_areas, array('none', 'desc:text')))
            throw new InvalidParam('protection_areas',"'$protection_areas'");
        $vars['protection_areas'] = $protection_areas;

        $tmp = $request->get_parameter('trackables');
        if (!$tmp) $tmp = 'none';
        if (!in_array($tmp, array('none', 'desc:list', 'desc:count')))
            throw new InvalidParam('trackables', "'$tmp'");
        $vars['trackables'] = $tmp;

        $tmp = $request->get_parameter('recommendations');
        if (!$tmp) $tmp = 'none';
        if (!in_array($tmp, array('none', 'desc:count')))
            throw new InvalidParam('recommendations', "'$tmp'");
        $vars['recommendations'] = $tmp;

        $lpc = $request->get_parameter('lpc');
        if ($lpc === null) $lpc = 10; # will be checked in services/caches/geocaches call

        $user_uuid = $request->get_parameter('user_uuid');

        # location_source (part 1 of 2)

        $location_source = $request->get_parameter('location_source');
        if (!$location_source)
        {
            $location_source = 'default-coords';
        }
        # Make sure location_source has prefix alt_wpt:
        if ($location_source != 'default-coords' && strncmp($location_source, 'alt_wpt:', 8) != 0)
        {
            throw new InvalidParam('location_source', '\''.$location_source.'\'');
        }

        # Make sure we have sufficient authorization
        if ($location_source == 'alt_wpt:user-coords' && $request->token == null)
        {
            throw new BadRequest("Level 3 Authentication is required to access 'alt_wpt:user-coords'.");
        }

        # Which fields of the services/caches/geocaches method do we need?

        $fields = 'code|name|location|date_created|url|type|status|size|size2|oxsize'.
            '|difficulty|terrain|description|hint2|rating|owner|url|internal_id'.
            '|protection_areas|short_description';
        if ($vars['images'] != 'none')
            $fields .= "|images";
        if (count($vars['attrs']) > 0)
            $fields .= "|attrnames|attr_acodes";
        if ($vars['trackables'] == 'desc:list')
            $fields .= "|trackables";
        elseif ($vars['trackables'] == 'desc:count')
            $fields .= "|trackables_count";
        if ($vars['alt_wpts'] == 'true' || $location_source != 'default-coords')
            $fields .= "|alt_wpts";
        if ($vars['recommendations'] != 'none')
            $fields .= "|recommendations|founds";
        if (count($vars['my_notes']) > 0)
            $fields .= "|my_notes";
        if ($vars['latest_logs'])
            $fields .= "|latest_logs";
        if ($vars['mark_found'])
            $fields .= "|is_found";

        $vars['caches'] = OkapiServiceRunner::call(
            'services/caches/geocaches', new OkapiInternalRequest(
                $request->consumer, $request->token, array(
                    'cache_codes' => $cache_codes,
                    'langpref' => $langpref,
                    'fields' => $fields,
                    'lpc' => $lpc,
                    'user_uuid' => $user_uuid,
                    'log_fields' => 'uuid|date|user|type|comment|internal_id|was_recommended'
                )
            )
        );

        # Get rid of invalid cache references.

        $valid = array();
        foreach ($vars['caches'] as $key => &$ref) {
            if ($ref !== null) {
                $valid[$key] = &$ref;
            }
        }
        $vars['caches'] = &$valid;
        unset($valid);

        # Get all the other data need.

        $vars['installation'] = OkapiServiceRunner::call(
            'services/apisrv/installation', new OkapiInternalRequest(
                new OkapiInternalConsumer(), null, array()
            )
        );
        $vars['cache_GPX_types'] = self::$cache_GPX_types;
        $vars['cache_GPX_sizes'] = self::$cache_GPX_sizes;

        if (count($vars['attrs']) > 0)
        {
            /* The user asked for some kind of attribute output. We'll fetch all
             * the data we MAY need. This is often far too much, but thanks to
             * caching, it will work fast. */

            $vars['attr_index'] = OkapiServiceRunner::call(
                'services/attrs/attribute_index', new OkapiInternalRequest(
                    $request->consumer, $request->token, array(
                        'only_locally_used' => 'true',
                        'langpref' => $langpref,
                        'fields' => 'name|gc_equivs'
                    )
                )
            );

            # prepare GS attribute data

            $vars['gc_attrs'] = in_array('gc:attrs', $vars['attrs']);
            $vars['gc_ocde_attrs'] = in_array('gc_ocde:attrs', $vars['attrs']);
            if ($vars['gc_attrs'] || $vars['gc_ocde_attrs'])
            {
                if ($vars['gc_ocde_attrs'])
                {
                    # As this is an OCDE compatibility feature, we use the same Pseudo-GS
                    # attribute names here as OCDE. Note that this code is specific to OCDE
                    # database; OCPL stores attribute names in a different way and may use
                    # different names for equivalent attributes.

                    $ocde_attrnames = Db::select_group_by('id',"
                        select id, name
                        from cache_attrib
                    ");
                    $attr_dict = AttrHelper::get_attrdict();
                }

                foreach ($vars['caches'] as &$cache_ref)
                {
                    $cache_ref['gc_attrs'] = array();
                    foreach ($cache_ref['attr_acodes'] as $acode)
                    {
                        $has_gc_equivs = false;
                        foreach ($vars['attr_index'][$acode]['gc_equivs'] as $gc)
                        {
                            # The assignment via GC-ID as array key will prohibit duplicate
                            # GC attributes, which can result from
                            # - assigning the same GC ID to multiple A-Codes,
                            # - contradicting attributes in one OC listing, e.g. 24/4 + not 24/7.

                            $cache_ref['gc_attrs'][$gc['id']] = $gc;
                            $has_gc_equivs = true;
                        }
                        if (!$has_gc_equivs && $vars['gc_ocde_attrs'])
                        {
                            # Generate an OCDE pseudo-GS attribute;
                            # see https://github.com/opencaching/okapi/issues/190 and
                            # https://github.com/opencaching/okapi/issues/271.
                            #
                            # Groundspeak uses ID 1..65 (as of June, 2013), and OCDE makeshift
                            # IDs start at 106, so there is space for 40 new GS attributes.

                            $internal_id = $attr_dict[$acode]['internal_id'];
                            $cache_ref['gc_attrs'][100 + $internal_id] = array(
                                'inc' => 1,
                                'name' => $ocde_attrnames[$internal_id][0]['name'],
                            );
                        }
                    }
                }
            }
        }

        /* OC sites always used internal user_ids in their generated GPX files.
         * This might be considered an error in itself (Groundspeak's XML namespace
         * doesn't allow that), but it very common (Garmin's OpenCaching.COM
         * also does that). Therefore, for backward-compatibility reasons, OKAPI
         * will do it the same way. See issue 174.
         *
         * Currently, the caches method does not expose "owner.internal_id" and
         * "latest_logs.user.internal_id" fields, we will read them manually
         * from the database here. */

        $dict = array();
        foreach ($vars['caches'] as &$cache_ref)
        {
            $dict[$cache_ref['owner']['uuid']] = true;
            if (isset($cache_ref['latest_logs']))
                foreach ($cache_ref['latest_logs'] as &$log_ref)
                    $dict[$log_ref['user']['uuid']] = true;
        }
        $rs = Db::query("
            select uuid, user_id
            from user
            where uuid in ('".implode("','", array_map('\okapi\Db::escape_string', array_keys($dict)))."')
        ");
        while ($row = Db::fetch_assoc($rs))
            $dict[$row['uuid']] = $row['user_id'];
        $vars['user_uuid_to_internal_id'] = &$dict;
        unset($dict);

        # location_source (part 2 of 2)

        if ($location_source != 'default-coords')
        {
            $location_change_prefix = $request->get_parameter('location_change_prefix');
            if (!$location_change_prefix) {
                $location_change_prefix = '# ';
            }
            # lets find requested coords
            foreach ($vars['caches'] as &$cache_ref)
            {
                foreach ($cache_ref['alt_wpts'] as $alt_wpt_key => $alt_wpt)
                {
                    if ('alt_wpt:'.$alt_wpt['type'] == $location_source)
                    {
                        # Switch locations between primary wpt and alternate wpt.
                        # Also alter the cache name and make sure to append a proper
                        # notice.

                        $original_location = $cache_ref['location'];
                        $cache_ref['location'] = $alt_wpt['location'];
                        $cache_ref['name_2'] = $location_change_prefix.$cache_ref['name'];
                        if ($location_source == "alt_wpt:user-coords") {
                            # In case of "user-coords", replace the default warning with a custom-tailored one.
                            $cache_ref['warning_prefix'] = _(
                                "<b>Geocache coordinates have been changed.</b> They have been replaced with ".
                                "your own custom coordinates which you have provided for this geocache."
                            );
                        } else {
                            # Default warning
                            $cache_ref['warning_prefix'] = _(
                                "<b>Geocache coordinates have been changed.</b> Currently they ".
                                "point to one of the alternate waypoints originally described as:"
                            ) . " " . $alt_wpt['description'];
                        }
                        # remove current alt waypoint
                        unset($cache_ref['alt_wpts'][$alt_wpt_key]);
                        # add original location as alternate
                        if ($vars['alt_wpts'])
                        {
                            $cache_ref['alt_wpts'][] = array(
                                'name' => $cache_ref['code'].'-DEFAULT-COORDS',
                                'location' => $original_location,
                                'type' => 'default-coords',
                                'type_name' => _("Original geocache location"),
                                'sym' => 'Block, Blue',
                                'description' => sprintf(_("Original (owner-supplied) location of the %s geocache"), $cache_ref['code']),
                            );
                        }
                        break;
                    }
                }
            }
        }

        # Do we need a GGZ index?

        if ($flags & self::FLAG_CREATE_GGZ_IDX) {

            # GGZ index consist of entries - one per each waypoint in the GPX file.
            # We will keep a list of all such entries here.

            $ggz_entries = array();

            foreach ($vars['caches'] as &$cache_ref)
            {
                # Every $cache_ref will also be holding a reference to its entry.
                # Note, that more attributes are added while processing gpsfile.tpl.php!

                if (!isset($cache_ref['ggz_entry'])) {
                    $cache_ref['ggz_entry'] = array();
                }
                $ggz_entry = &$cache_ref['ggz_entry'];
                $ggz_entries[] = &$ggz_entry;

                $ggz_entry['code'] = $cache_ref['code'];
                $ggz_entry['name'] = isset($cache_ref['name_2']) ? $cache_ref['name_2'] : $cache_ref['name'];
                $ggz_entry['type'] = $vars['cache_GPX_types'][$cache_ref['type']];
                list($lat, $lon) = explode("|", $cache_ref['location']);
                $ggz_entry['lat'] = $lat;
                $ggz_entry['lon'] = $lon;

                $ggz_entry['ratings'] = array();
                $ratings_ref = &$ggz_entry['ratings'];
                if (isset($cache_ref['rating'])){
                   $ratings_ref['awesomeness'] = $cache_ref['rating'];
                }
                $ratings_ref['difficulty'] = $cache_ref['difficulty'];
                if (!isset($cache_ref['size'])) {
                    $ratings_ref['size'] = 0; // Virtual, Event
                } else if ($cache_ref['oxsize'] !== null) { // is this ox size one-to-one?
                    $ratings_ref['size'] = $cache_ref['oxsize'];
                }
                $ratings_ref['terrain'] = $cache_ref['terrain'];

                if ($vars['mark_found'] && $cache_ref['is_found']) {
                    $ggz_entry['found'] = true;
                }

                # Additional waypoints. Currently, we're not 100% sure if their entries should
                # be included in the GGZ file (the format is undocumented).

                if (isset($cache_ref['alt_wpts'])) {
                    $idx = 1;
                    foreach ($cache_ref['alt_wpts'] as &$alt_wpt_ref) {
                        if (!isset($alt_wpt_ref['ggz_entry'])) {
                            $alt_wpt_ref['ggz_entry'] = array();
                        }
                        $ggz_entry = &$alt_wpt_ref['ggz_entry'];
                        $ggz_entries[] = &$ggz_entry;

                        $ggz_entry['code'] = $cache_ref['code'] . '-' . $idx;
                        $ggz_entry['name'] = $alt_wpt_ref['type_name'];
                        $ggz_entry['type'] = $alt_wpt_ref['sym'];
                        list($lat, $lon) = explode("|", $alt_wpt_ref['location']);
                        $ggz_entry['lat'] = $lat;
                        $ggz_entry['lon'] = $lon;

                        $idx++;
                    }
                }
            }
        }

        ob_start();
        Okapi::gettext_domain_init(explode("|", $langpref)); # Consumer gets properly localized GPX file.
        include 'gpxfile.tpl.php';
        Okapi::gettext_domain_restore();

        $result = array('gpx' => ob_get_clean());
        if ($flags & self::FLAG_CREATE_GGZ_IDX) {
            $result['ggz_entries'] = $ggz_entries;
        }

        return $result;
    }
}
