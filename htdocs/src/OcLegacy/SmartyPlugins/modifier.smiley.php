<?php
/**
 * Smarty plugin
 */
require_once __DIR__ . '/../../../lib2/smiley.inc.php';

/**
 * @param $string
 * @return mixed
 */
function smarty_modifier_smiley($string)
{
    global $smiley;

    return str_replace($smiley['text'], $smiley['spaced_image'], $string);
}
