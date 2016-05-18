<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Net/IDNA2.php';

// !! THIS CODE IS ALSO USED IN OKAPI !!
// Any changes must be tested with OKAPI services/logs/submit method.
// Avoid to include any other OC.de code here.

// Also used for lib1 code.


class OcHTMLPurifier extends HTMLPurifier
{
    // $opt needs to be passed as parameter here because it resides in another
    // namespace in OKAPI. All options used here must be included in the
    // $opt['html_purifier'] array.

    public function __construct($opt)
    {
        // prepare config
        /** @var HTMLPurifier_Config $config */
        $config = HTMLPurifier_Config::createDefault();

        // set cache directory
        $config->set('Cache.SerializerPath', $opt['html_purifier']['cache_path']);

        // adjust URI filtering to fix issue #89 (enable special chars in URIs)
        $config->set('Core.EnableIDNA', true);

        // allow comments
        $config->set('HTML.AllowedCommentsRegexp', '/.*/');

        // enable href target='_blank'
        $config->set(
            'Attr.AllowedFrameTargets',
            [
                '_blank',
                'blank'
            ]
        );

        // enable ids/names with namespace 'custom_'
        $config->set('Attr.EnableID', true);
        $config->set('Attr.IDPrefix', 'custom_');

        // enable 'display' and 'visibility' styles for mystery descriptions
        $config->set('CSS.AllowTricky', true);                // + display, visibility, overflow
        $config->set('CSS.ForbiddenProperties', 'overflow');  // - overflow

        // prepare additional definitions
        $def = $config->getHTMLDefinition(true);

        // add tags
        $def->addElement(
            'fieldset',
            'Block',
            'Flow',
            'Common' /* ,array('disabled' => 'Enum#disabled', 'name' => 'ID') */
        ); //  HTML5 attribs currently not supported by TinyMCE
        $def->addElement('legend', 'Inline', 'Flow', 'Common');
        $def->addElement('q', 'Inline', 'Inline', 'Common', ['cite' => 'URI']);
        $def->addElement('strike', 'Inline', 'Inline', 'Common');   // -> wird in CSS umgewandelt
        $def->addElement(
            'area',
            'Inline',
            'Empty',
            'Common',
            [
                'alt' => 'CDATA',
                'coords' => 'CDATA',
                'href' => 'URI',
                'shape' => 'Enum#default,rect,circle,poly',
                'target' => 'Enum#_blank,blank'
            ]
        );
        $def->addElement('map', 'Block', new HTMLPurifier_ChildDef_Optional('area'), 'Common', ['name' => 'ID']);

        // add attributes
        $def->addAttribute('img', 'usemap', 'CDATA');

        // create parent object with config
        parent::__construct($config);
    }


    public function purify($text, $config = null)
    {
        // HTMLPurifier deletes spaces between images; apply space protection:
        do {
            $text0 = $text;
            $text = mb_ereg_replace(">[\s\t]+<img", ">[s[p[a[c[e]]]]]<img", $text);
        } while ($text != $text0);

        $text = parent::purify($text, $config);

        return str_replace("[s[p[a[c[e]]]]]<img", " <img", $text);
    }
}
