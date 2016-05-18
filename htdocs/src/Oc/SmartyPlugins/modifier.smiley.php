<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 */

require_once $opt['rootpath'] . 'lib2/smiley.inc.php';

function smarty_modifier_smiley($string)
{
    global $smiley;

    return str_replace($smiley['text'], $smiley['spaced_image'], $string);
}
