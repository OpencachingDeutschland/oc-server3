<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/HTMLPurifier/library/HTMLPurifier.auto.php');

class OcHTMLPurifier extends HTMLPurifier
{

    function __construct()
    {
    	global $opt;
    	
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