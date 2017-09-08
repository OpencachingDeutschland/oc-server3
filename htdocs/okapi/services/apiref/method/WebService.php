<?php

namespace okapi\services\apiref\method;

use Exception;
use okapi\core\Consumer\OkapiInternalConsumer;
use okapi\core\Exception\InvalidParam;
use okapi\core\Exception\ParamMissing;
use okapi\core\Okapi;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiInternalRequest;
use okapi\core\Request\OkapiRequest;
use okapi\Settings;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 0
        );
    }

    private static function arg_desc($arg_node)
    {
        $attrs = $arg_node->attributes();
        $result = array(
            'name' => (string)$attrs['name'],
            'is_required' => $arg_node->getName() == 'req',
            'is_deprecated' => (isset($attrs['class']) && (strpos($attrs['class'], 'deprecated') !== false)),
            'class' => 'public',
            'infotags' => [],
            'description' =>
                (isset($attrs['default']) ? ("<p>Default value: <b>".$attrs['default']."</b></p>") : "").
                self::get_inner_xml($arg_node),
        );
        if (isset($attrs['infotags'])) {
            foreach (explode(" ", (string)$attrs['infotags']) as $infotag) {
                switch ($infotag) {
                    case 'ocpl-specific':
                    case 'ocde-specific':
                        $result['infotags'][] = $infotag;
                        break;
                    default:
                        throw new Exception("Invalid infotag '".$infotag." in $methodname.xml");
                }
            }
        }
        return $result;
    }

    private static function get_inner_xml($node)
    {
        /* Fetch as <some-node>content</some-node>, extract content. */

        $s = $node->asXML();
        $start = strpos($s, ">") + 1;
        $length = strlen($s) - $start - (3 + strlen($node->getName()));
        $s = substr($s, $start, $length);

        /* Find and replace %okapi:plugins%. */

        $s = preg_replace_callback('~%OKAPI:([a-z:/_#-]+)%~', array("self", "plugin_callback"), $s);

        return $s;
    }

    /**
     * You can use the following syntax:
     *
     * <a href="%OKAPI:docurl:fragment%">any text</a> - to reference fragment of introducing
     * documentation
     *
     * <a href="%OKAPI:methodref:methodname%">any text</a> - to reference any other method
     *
     * <a href="%OKAPI:methodref:methodname#html_anchor%">any text</a> - to reference
     * any HTML anchor in other method
     *
     * <a href="%OKAPI:methodref:#html_anchor%">any text</a> - to reference any HTML
     * anchor within current document
     *
     * <a href="%OKAPI:methodargref:methodname#argument_name%">any text</a> - to
     * reference argument of another method
     *
     * <a href="%OKAPI:methodargref:#argument_name%">any text</a> - to reference
     * argument within current method
     *
     * <a href="%OKAPI:methodretref:methodname#returned_key%">any text</a> - to
     * reference returned value of another method
     *
     * <a href="%OKAPI:methodretref:#returned_key%">any text</a> - to reference
     * returned value within current method
     *
     * %OKAPI:infotag:TAGNAME% - to output a HTML with a proper infotag "badge".
     * TAGNAME must match one of the infotags defined in services/apiref/method.
     *
     * NOTE!
     *
     * Since returned JSON dictionaries are not standardized (they are simply plain
     * HTML in the docs), to reference returned values you must manually create an
     * anchor prefixed with ret_, i.e. (HTML snippet): <li
     * id="ret_alt_wpts"><p><b>alt_wpts</b> - list of alternate/additional
     * waypoints</...>  and access it with (HTML snippet): <a
     * href="%OKAPI:methodretref:#alt_wpts%">any text</a>.
     */
    public static function plugin_callback($matches)
    {
        $input = $matches[1];
        $arr = explode(":", $input);
        $plugin_name = $arr[0];

        switch ($plugin_name) {
            case 'docurl':
                $fragment = $arr[1];
                return Settings::get('SITE_URL')."okapi/introduction.html#".$fragment;
            case 'methodref':
            case 'methodargref':
            case 'methodretref':
                $elements = explode('#', $arr[1]);
                $result = '';
                if ($elements[0] != '')
                {
                    $result .= Settings::get('SITE_URL')."okapi/".$elements[0].'.html';
                }
                if (count($elements) > 1)
                {
                    $result .= '#';
                    switch ($plugin_name) {
                        case 'methodargref':
                            $result .= 'arg_';
                            break;
                        case 'methodretref':
                            $result .= 'ret_';
                            break;
                    }
                    $result .= $elements[1];
                }
                return $result;
            case 'infotag':
                return Okapi::format_infotags([$arr[1]]);
            default:
                throw new Exception("Unknown plugin: ".$input);
        }
    }

    public static function call(OkapiRequest $request)
    {
        $methodname = $request->get_parameter('name');
        if (!$methodname)
            throw new ParamMissing('name');
        if (!preg_match("#^services/[0-9a-z_/]*$#", $methodname))
            throw new InvalidParam('name');
        if (!OkapiServiceRunner::exists($methodname))
            throw new InvalidParam('name', "Method does not exist: '$methodname'.");
        $options = OkapiServiceRunner::options($methodname);
        if (!isset($options['min_auth_level']))
            throw new Exception("Method $methodname is missing a required 'min_auth_level' option!");
        $docs = simplexml_load_string(OkapiServiceRunner::docs($methodname));
        $exploded = explode("/", $methodname);
        $result = array(
            'name' => $methodname,
            'short_name' => end($exploded),
            'ref_url' => Settings::get('SITE_URL')."okapi/$methodname.html",
            'auth_options' => array(
                'min_auth_level' => $options['min_auth_level'],
                'oauth_consumer' => $options['min_auth_level'] >= 2,
                'oauth_token' => $options['min_auth_level'] >= 3,
            ),
            "infotags" => [],
        );
        if (!$docs->brief)
            throw new Exception("Missing <brief> element in the $methodname.xml file.");
        if ($docs->brief != self::get_inner_xml($docs->brief))
            throw new Exception("The <brief> element may not contain HTML markup ($methodname.xml).");
        if (strlen($docs->brief) > 80)
            throw new Exception("The <brief> description may not be longer than 80 characters ($methodname.xml).");
        if (strpos($docs->brief, "\n") !== false)
            throw new Exception("The <brief> element may not contain new-lines ($methodname.xml).");
        if (substr(trim($docs->brief), -1) == '.')
            throw new Exception("The <brief> element should not end with a dot ($methodname.xml).");
        $result['brief_description'] = self::get_inner_xml($docs->brief);
        if ($docs->{'issue-id'})
            $result['issue_id'] = (string)$docs->{'issue-id'};
        else
            $result['issue_id'] = null;
        if (!$docs->desc)
            throw new Exception("Missing <desc> element in the $methodname.xml file.");
        $result['description'] = self::get_inner_xml($docs->desc);
        if ($docs->infotags) {
            foreach (explode(" ", (string)$docs->infotags) as $infotag) {
                switch ($infotag) {
                    case 'ocpl-specific':
                    case 'ocde-specific':
                        $result['infotags'][] = $infotag;
                        break;
                    default:
                        throw new Exception("Invalid infotag '".$infotag." in $methodname.xml");
                }
            }
        }
        $result['arguments'] = array();
        foreach ($docs->req as $arg) { $result['arguments'][] = self::arg_desc($arg); }
        foreach ($docs->opt as $arg) { $result['arguments'][] = self::arg_desc($arg); }
        foreach ($docs->{'import-params'} as $import_desc)
        {
            $attrs = $import_desc->attributes();
            $referenced_methodname = $attrs['method'];
            $referenced_method_info = OkapiServiceRunner::call('services/apiref/method',
                new OkapiInternalRequest(new OkapiInternalConsumer(), null, array('name' => $referenced_methodname)));
            $include_list = isset($attrs['params']) ? explode("|", $attrs['params']) : null;
            $exclude_list = isset($attrs['except']) ? explode("|", $attrs['except']) : array();
            foreach ($referenced_method_info['arguments'] as $arg)
            {
                if ($arg['class'] == 'common-formatting')
                    continue;
                if (($include_list === null) && (count($exclude_list) == 0))
                {
                    $arg['description'] = "<i>Inherited from <a href='".$referenced_method_info['ref_url'].
                        "#arg_". $arg['name'] . "'>".$referenced_method_info['name']."</a> method.</i>";
                }
                elseif (
                    (($include_list === null) || in_array($arg['name'], $include_list))
                    && (!in_array($arg['name'], $exclude_list))
                ) {
                    $arg['description'] = "<i>Same as in the <a href='".$referenced_method_info['ref_url'].
                        "#arg_". $arg['name'] . "'>".$referenced_method_info['name']."</a> method.</i>";
                } else {
                    continue;
                }
                $arg['class'] = 'inherited';
                $result['arguments'][] = $arg;
            }
        }
        if ($docs->{'common-format-params'})
        {
            $result['arguments'][] = array(
                'name' => 'format',
                'is_required' => false,
                'is_deprecated' => false,
                'class' => 'common-formatting',
                'infotags' => [],
                'description' => "<i>Standard <a href='".Settings::get('SITE_URL')."okapi/introduction.html#common-formatting'>common formatting</a> argument.</i>"
            );
            $result['arguments'][] = array(
                'name' => 'callback',
                'is_required' => false,
                'is_deprecated' => false,
                'class' => 'common-formatting',
                'infotags' => [],
                'description' => "<i>Standard <a href='".Settings::get('SITE_URL')."okapi/introduction.html#common-formatting'>common formatting</a> argument.</i>"
            );
        }
        foreach ($result['arguments'] as &$arg_ref)
            if ($arg_ref['is_deprecated'])
                $arg_ref['class'] .= " deprecated";
        if (!$docs->returns)
            throw new Exception("Missing <returns> element in the $methodname.xml file. ".
                "If your method does not return anything, you should document in nonetheless.");
        $result['returns'] = self::get_inner_xml($docs->returns);
        return Okapi::formatted_response($request, $result);
    }
}
