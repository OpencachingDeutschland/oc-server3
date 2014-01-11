<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once __DIR__ . '/../lib/core.php';

// !! THIS CODE IS ALSO USED IN OKAPI !!
// Any changes must be tested with OKAPI services/logs/submit method.
// Avoid to include any other OC.de code here.


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
