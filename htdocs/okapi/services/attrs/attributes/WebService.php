<?php

namespace okapi\services\attrs\attributes;

use okapi\Db;
use okapi\Exception\InvalidParam;
use okapi\Exception\ParamMissing;
use okapi\Okapi;
use okapi\Request\OkapiRequest;
use okapi\services\attrs\AttrHelper;
use okapi\Settings;


class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    private static $valid_field_names = array(
        'acode', 'name', 'names', 'description', 'descriptions', 'gc_equivs',
        'is_locally_used', 'is_deprecated', 'local_icon_url', 'is_discontinued'
    );

    public static function call(OkapiRequest $request)
    {
        # Read the parameters.

        $acodes = $request->get_parameter('acodes');
        if (!$acodes) throw new ParamMissing('acodes');
        $acodes = explode("|", $acodes);

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langprefs = explode("|", $langpref);

        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "name";
        $fields = explode("|", $fields);
        foreach ($fields as $field)
        {
            if (!in_array($field, self::$valid_field_names))
                throw new InvalidParam('fields', "'$field' is not a valid field code.");
        }

        $forward_compatible = $request->get_parameter('forward_compatible');
        if (!$forward_compatible) $forward_compatible = "true";
        if (!in_array($forward_compatible, array("true", "false")))
            throw new InvalidParam('forward_compatible');
        $forward_compatible = ($forward_compatible == "true");

        # Load the attributes (all of them).

        $attrdict = AttrHelper::get_attrdict();

        # For each A-code, check if it exists, filter its fields and add it
        # to the results.

        $results = array();
        foreach ($acodes as $acode)
        {
            /* Please note, that the $attr variable from the $attrdict dictionary
             * below is NOT fully compatible with the interface of the "attribute"
             * method. Some of $attr's fields are private and should not be exposed,
             * other fields don't exist and have to be added dynamically! */

            if (isset($attrdict[$acode])) {
                $attr = $attrdict[$acode];
            } elseif ($forward_compatible) {
                $attr = AttrHelper::get_unknown_placeholder($acode);
            } else {
                $results[$acode] = null;
                continue;
            }

            # Fill langpref-specific fields.

            $attr['name'] = Okapi::pick_best_language($attr['names'], $langprefs);
            $attr['description'] = Okapi::pick_best_language($attr['descriptions'], $langprefs);

            # Fill some other fields (not kept in the cached attrdict).

            $attr['is_locally_used'] = ($attr['internal_id'] !== null);
            $attr['is_deprecated'] = $attr['is_discontinued'];  // deprecated and undocumetned field, see issue 70

            # Add to results.

            $results[$acode] = $attr;
        }

        # If the user wanted local_icon_urls, fetch them now. (We cannot cache them
        # in the $attrdict because currently we have no way of knowing then they
        # change.)

        if (in_array('local_icon_url', $fields))
        {
            $tmp = Db::select_all("
                select id, icon_large
                from cache_attrib
            ");
            $map = array();
            foreach ($tmp as &$row_ref) {
                $map[$row_ref['id']] = &$row_ref;
            }
            $prefix = Settings::get('SITE_URL');
            foreach ($results as &$attr_ref) {
                if ($attr_ref === null) {
                    continue;
                }
                $internal_id = $attr_ref['internal_id'];
                if (isset($map[$internal_id])) {
                    $row = $map[$internal_id];
                    $attr_ref['local_icon_url'] = $prefix.$row['icon_large'];
                } else {
                    $attr_ref['local_icon_url'] = null;
                }
            }
        }

        # Filter the fields.

        foreach ($results as &$attr_ref) {
            if ($attr_ref === null) {
                continue;
            }
            $clean_row = array();
            foreach ($fields as $field)
                $clean_row[$field] = $attr_ref[$field];
            $attr_ref = $clean_row;
        }

        return Okapi::formatted_response($request, $results);
    }
}
