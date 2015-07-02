<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  For more information about this smarty-extension see
 *  prefilter.t.php
 ***************************************************************************/
 
/**
 * Replaces arguments in a string with their values.
 * Arguments are represented by % followed by their number.
 *
 * @param	string	Source string
 * @param	mixed	Arguments, can be passed in an array or through single variables.
 * @returns	string	Modified string
 */
function smarty_gettext_strarg($str)
{
	$tr = array();
	$p = 0;

	for ($i=1; $i < func_num_args(); $i++) {
		$arg = func_get_arg($i);
		
		if (is_array($arg)) {
			foreach ($arg as $aarg) {
				$tr['%'.++$p] = $aarg;
			}
		} else {
			$tr['%'.++$p] = $arg;
		}
	}
	
	return strtr($str, $tr);
}

/**
 * Smarty block function, provides gettext support for smarty.
 *
 * The block content is the text that should be translated.
 *
 * Any parameter that is sent to the function will be represented as %n in the translation text, 
 * where n is 1 for the first parameter. The following parameters are reserved:
 *   - escape - Valid is "js" to escape a string for usage inside JS string
 *   - plural - The plural version of the text (2nd parameter of ngettext())
 *   - count - The item count for plural mode (3rd parameter of ngettext())
 */
function smarty_block_t($params, $text, &$smarty, &$repeat)
{
	global $opt;

	if ($repeat) return;

	$escape = isset($params['escape']) ? $params['escape'] : '';
	unset($params['escape']);

	// use plural if required parameters are set
	if (isset($params['count']) && isset($params['plural']) && $params['count']!=1)
	{
		$text = $params['plural'];
	}
	unset($params['plural']);
	unset($params['count']);

	// run strarg if there are parameters
	if (count($params))
		$text = smarty_gettext_strarg($text, $params);

	// escape the string, now
	// see also modifier.escpapejs.php
	if ($escape == 'js')
	{
		$text = str_replace('\\', '\\\\', $text);
		$text = str_replace('\'', '\\\'', $text);
		$text = str_replace('"', '&quot;', $text);
	}

	return $text;
}

?>