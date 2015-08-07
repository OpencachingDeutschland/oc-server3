<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once(__DIR__ . '/HTMLPurifier/library/HTMLPurifier.auto.php');
require_once(__DIR__ . '/Net/IDNA2.php');

// !! THIS CODE IS ALSO USED IN OKAPI !!
// Any changes must be tested with OKAPI services/logs/submit method.
// Avoid to include any other OC.de code here.

// Also used for lib1 code.


class OcHTMLPurifier extends HTMLPurifier
{
	// $opt needs to be passed as parameter here because it resides in another
	// namespace in OKAPI. All options used here must be included in the
	// $opt['html_purifier'] array.

	function __construct($opt)
	{
		// prepare config
		$config = HTMLPurifier_Config::createDefault();

		// set cache directory
		$config->set('Cache.SerializerPath', $opt['html_purifier']['cache_path']);

		// adjust URI filtering to fix issue #89 (enable special chars in URIs)
		$config->set('Core.EnableIDNA', true);

		// allow comments
		$config->set('HTML.AllowedCommentsRegexp', '/.*/');

		// enable href target='_blank'
		$config->set('Attr.AllowedFrameTargets', array('_blank','blank'));

		// enable ids/names with namespace 'custom_'
		$config->set('Attr.EnableID', true);
		$config->set('Attr.IDPrefix', 'custom_');

		// enable 'display' and 'visibility' styles for mystery descriptions
		$config->set('CSS.AllowTricky', true);                // + display, visibility, overflow
		$config->set('CSS.ForbiddenProperties', 'overflow');  // - overflow 

		// prepare additional definitions
		$def = $config->getHTMLDefinition(true);

		// add tags
		$def->addElement('fieldset', 'Block', 'Flow', 'Common' /* ,array('disabled' => 'Enum#disabled', 'name' => 'ID') */ ); //  HTML5 attribs currently not supported by TinyMCE
		$def->addElement('legend', 'Inline', 'Flow', 'Common');
		$def->addElement('q', 'Inline', 'Inline', 'Common', array('cite' => 'URI'));
		$def->addElement('strike', 'Inline', 'Inline', 'Common');   // -> wird in CSS umgewandelt
		$def->addElement('area', 'Inline', 'Empty', 'Common', array('alt' => 'CDATA', 'coords' => 'CDATA', 'href' => 'URI', 'shape' => 'Enum#default,rect,circle,poly', 'target' => 'Enum#_blank,blank'));
		$def->addElement('map', 'Block', new HTMLPurifier_ChildDef_Optional('area'), 'Common', array('name' => 'ID'));

		// add attributes
		$def->addAttribute('img', 'usemap', 'CDATA');

		// create parent object with config
		parent::__construct($config);
	}
}


// Old code from useroptions.class.php, was formerly used to purify profile
// description text and may be useful somewhere ...?

/*
  function tidy_html_description($text)
  {
    $options = array("input-encoding" => "utf8", "output-encoding" => "utf8", "output-xhtml" => true, "doctype" => "omit", "show-body-only" => true, "char-encoding" => "utf8", "quote-ampersand" => true, "quote-nbsp" => true, "wrap" => 0);
    $config = HTMLPurifier_Config::createDefault();
    $cssDefinition = $config->getCSSDefinition();

    $cssDefinition->info['position'] = new
      HTMLPurifier_AttrDef_Enum(array('absolute', 'fixed', 'relative', 'static', 'inherit'), false);

    $cssDefinition->info['left'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
      new HTMLPurifier_AttrDef_CSS_Length(),
      new HTMLPurifier_AttrDef_CSS_Percentage()
    ));

    $cssDefinition->info['right'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
      new HTMLPurifier_AttrDef_CSS_Length(),
      new HTMLPurifier_AttrDef_CSS_Percentage()
    ));

    $cssDefinition->info['top'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
      new HTMLPurifier_AttrDef_CSS_Length(),
      new HTMLPurifier_AttrDef_CSS_Percentage()
    ));

    $cssDefinition->info['bottom'] = new HTMLPurifier_AttrDef_CSS_Composite(array(
      new HTMLPurifier_AttrDef_CSS_Length(),
      new HTMLPurifier_AttrDef_CSS_Percentage()
    ));

    $purifier = new HTMLPurifier($config);
    $clean_html = $purifier->purify($text);
    return $clean_html;
  }
}
*/

?>