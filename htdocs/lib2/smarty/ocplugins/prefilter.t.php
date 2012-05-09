<?php
/***************************************************************************
 * You can find the license in the docs directory
 *
 * Unicode Reminder メモ
 *
 * Replacement for smarty-gettext
 *
 * Advantages:
 *  - caching of gettext-translations within nocache-sections
 *  - gettext is called at compile-time and not at page redering
 *    this improves performance when caching is disabled, too
 *  - translations stored in mysql-database
 *
 * Disadvantages:
 *  - you have to clear the cache after changing translations
 *  - use the locale in compile_id (maybe your compile_dir will grow)
 *  - only one plural version can be supplied
 *
 * Working:
 *
 * Block-definition:
 * {t[ count=<number> plural=<plural-text>][ 1=<value1>[ 2=<value2>[...]]]}<text-to-translate>{/t}
 *
 * <number>             ... number=1 means "use singluar", number!=1 means "use plural"
 * <plural>             ... plural version of <text-to-translate>
 * <value[1..n]>        ... values of parameters (can be string, number or smarty-variable with modifiers)
 * <text-to-translate>  ... text that has to be translated. If no translation exists, this text will be used
 *                          parameters <value[1..n]> can be used with %1 ... %n
 *
 * Expamle:
 * {t 1="text"}my %1{/t}
 *
 * If no plural is given, the block will be replaced by the appropriate translation
 * If plural is given, <text-to-translate> and <plural-text> will be translated, but block will persist and
 * processed by block.t at rendering time.
 *
 * Original idea by Sagi Bashari <sagi@boom.org.il>
 * see http://sourceforge.net/projects/smarty-gettext/
 *
 * Concept was heavily modified by Opencaching.de
 *
 * Copyright 2007 Opencaching.de
 */

/*
 * Smarty plugin for gettext compilation
 *
 * Find all {t}...{/t} and translate its input with gettext
 *
 */
function smarty_prefilter_t($source, &$smarty)
{
	$output = '';
	$output_start = 0;

	$end = 0;
	while (($start = smarty_prefilter_t_strpos_multi($source, array($smarty->left_delimiter . 't ', $smarty->left_delimiter . 't' . $smarty->right_delimiter), $end)) !== false)
	{
		$end = mb_strpos($source, $smarty->left_delimiter . '/t' . $smarty->right_delimiter, $start);
		$block_t = mb_substr($source, $start, $end - $start);

		$messgage_start = mb_strrpos($block_t, '}') + 1;
		$block_t = smarty_prefilter_t_process_block(mb_substr($block_t, 0, $messgage_start), mb_substr($block_t, $messgage_start), $smarty, 0);

		$output .= mb_substr($source, $output_start, $start - $output_start);
		$output_start = $end + mb_strlen($smarty->left_delimiter . $smarty->right_delimiter) + 2;

		$output .= $block_t;
	}
	$output .= mb_substr($source, $output_start);

	return $output;
}

/* $block ... {t[ a=$a|nbsp b="a" ...]}
 *
 */
function smarty_prefilter_t_process_block($block, $message, &$smarty, $line)
{
	if ($message != '')
	{
		$start_attr = mb_strpos($block, ' ');
		if ($start_attr !== false)
		{
			if ((mb_substr($block, 0, 1) != $smarty->left_delimiter) || $start_attr == 1 || mb_substr($block, -1, 1) != $smarty->right_delimiter)
				$smarty->_syntax_error("internal processing error: '$block'", E_USER_ERROR, __FILE__, __LINE__);
			$block = mb_substr($block, $start_attr + 1, mb_strlen($block) - $start_attr - 2);

			// parse the attributes
			$attrs = smarty_prefilter_t_parse_attrs($block, $smarty);

			if (isset($attrs['plural']) && isset($attrs['count']))
			{
				$message = smarty_prefilter_t_gettext($message, array(), $smarty, $line);

				if ((mb_substr($attrs['plural'], 0, 1) == '"') && mb_substr($attrs['plural'], -1, 1) == '"')
					$attrs['plural'] = mb_substr($attrs['plural'], 1, mb_strlen($attrs['plural'])-2);
				$attrs['plural'] = smarty_prefilter_t_gettext($attrs['plural'], array(), $smarty, $line);

				// rebuild block with replaced plural
				$block = '';
				foreach ($attrs AS $k => $v)
				{
					if ($block != '') $block .= ' ';
					$block .= $k . '=' . $v;
				}

				// pass it to block.t
				return $smarty->left_delimiter . 't ' . $block . $smarty->right_delimiter . $message . $smarty->left_delimiter . '/t' . $smarty->right_delimiter;
			}
			unset($attrs['plural']);
			unset($attrs['count']);

			$message = smarty_prefilter_t_gettext($message, $attrs, $smarty, $line);
		}
		else
		{
			$message = smarty_prefilter_t_gettext($message, array(), $smarty, $line);
		}
	}

	return $message;
}

/**
  * Parse attribute string
  *   copied from Smarty_comiler.class.php
  *   we need the same source, expect _parse_vars_props at the end
  *
  * @param string $tag_args
  * @return array
  */
function smarty_prefilter_t_parse_attrs($tag_args, &$smarty)
{

    /* Tokenize tag attributes. */
    preg_match_all('~(?:' . $smarty->_obj_call_regexp . '|' . $smarty->_qstr_regexp . ' | (?>[^"\'=\s]+)
                      )+ |
                      [=]
                    ~x', $tag_args, $match);
    $tokens       = $match[0];

    $attrs = array();
    /* Parse state:
        0 - expecting attribute name
        1 - expecting '='
        2 - expecting attribute value (not '=') */
    $state = 0;

    foreach ($tokens as $token) {
        switch ($state) {
            case 0:
                /* If the token is a valid identifier, we set attribute name
                    and go to state 1. */
                if (preg_match('~^\w+$~', $token)) {
                    $attr_name = $token;
                    $state = 1;
                } else
                    $smarty->_syntax_error("invalid attribute name: '$token'", E_USER_ERROR, __FILE__, __LINE__);
                break;

            case 1:
                /* If the token is '=', then we go to state 2. */
                if ($token == '=') {
                    $state = 2;
                } else
                    $smarty->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
                break;

            case 2:
                /* If token is not '=', we set the attribute value and go to
                    state 0. */
                if ($token != '=') {
                    /* We booleanize the token if it's a non-quoted possible
                        boolean value. */
                    if (preg_match('~^(on|yes|true)$~', $token)) {
                        $token = 'true';
                    } else if (preg_match('~^(off|no|false)$~', $token)) {
                        $token = 'false';
                    } else if ($token == 'null') {
                        $token = 'null';
                    } else if (preg_match('~^' . $smarty->_num_const_regexp . '|0[xX][0-9a-fA-F]+$~', $token)) {
                        /* treat integer literally */
                    } else if (!preg_match('~^' . $smarty->_obj_call_regexp . '|' . $smarty->_var_regexp . '(?:' . $smarty->_mod_regexp . ')*$~', $token)) {
                        /* treat as a string, double-quote it escaping quotes */
                        $token = '"'.addslashes($token).'"';
                    }

                    $attrs[$attr_name] = $token;
                    $state = 0;
                } else
                    $smarty->_syntax_error("'=' cannot be an attribute value", E_USER_ERROR, __FILE__, __LINE__);
                break;
        }
        $last_token = $token;
    }

    if($state != 0) {
        if($state == 1) {
            $smarty->_syntax_error("expecting '=' after attribute name '$last_token'", E_USER_ERROR, __FILE__, __LINE__);
        } else {
            $smarty->_syntax_error("missing attribute value", E_USER_ERROR, __FILE__, __LINE__);
        }
    }

		// this call would translate the attrs to php code
		// we dont need it, because its a prefilter ...
    //$smarty->_parse_vars_props($attrs);

    return $attrs;
}

function smarty_prefilter_t_strpos_multi($haystack, $needles)
{
	$arg = func_get_args();
	$start = false;

	foreach ($needles AS $needle)
	{
		$thisstart = mb_strpos($haystack, $needle, $arg[2]);
		if ($start == false)
			$start = $thisstart;
		else if ($thisstart == false)
		{
		}
		else if ($start > $thisstart)
			$start = $thisstart;
	}

	return $start;
}

function smarty_prefilter_t_gettext($message, $attrs, &$smarty, $line)
{
	global $opt, $translate;

	if (!isset($translate))
		return $message;

	$trans = $translate->t($message, $opt['template']['style'], '', 0);

	// TODO concept escapement
	if (isset($attrs['escape'])) unset($attrs['escape']);
	if (isset($attrs['plural'])) unset($attrs['plural']);
	if (isset($attrs['count'])) unset($attrs['count']);

	// replace params
	$number = 1;
	foreach ($attrs AS $attr)
	{
		if (is_numeric($attr))
			$trans = mb_ereg_replace('%' . $number, $attr, $trans);
		else
			$trans = mb_ereg_replace('%' . $number, $smarty->left_delimiter . $attr . $smarty->right_delimiter, $trans);

		$number++;
	}

	return $trans;
}
?>