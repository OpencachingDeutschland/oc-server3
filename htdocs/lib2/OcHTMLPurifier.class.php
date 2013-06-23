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
?>