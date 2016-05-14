<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Displays the Chat/IRC using iframe of freenode.net, escaping usernames
 ***************************************************************************/

require('./lib2/web.inc.php');
$sUserCountry = $login->getUserCountry();

$tpl->name = 'webchat';
$tpl->menuitem = MNU_CHAT;

$tpl->caching = false;
$tpl->cache_id = $sUserCountry;

// check loggedin and set username for chat
$chatusername = $translate->t('Guest', '', basename(__FILE__), __LINE__) . mt_rand(100, 999);
if ($login->userid != 0) {
    $chatusername = urlEncodeString(ircConvertString($login->username));
}

// prepare iframe-URL
$chatiframeurl = str_replace('{chatusername}', $chatusername, $opt['chat']['url']);
$chatiframeurl = preg_replace('/^https?:/', $opt['page']['protocol'] . ':', $chatiframeurl);

// assign to template
$tpl->assign('chatiframeurl', $chatiframeurl);
$tpl->assign('chatiframewidth', $opt['chat']['width']);
$tpl->assign('chatiframeheight', $opt['chat']['height']);

$tpl->display();


/*
 * OC allows ISO-8859-1 letters in usernames and
 *   0-9 . - _ @ = ) ( / \ & * + ~ #
 *
 * IRC allows ASCII letters in nick and
 *   0-9 _ - \ [ ] { } ^ ` |
 *
 * so we have to convert the following chars before urlencoding it:
 *   . @ = ) ( / & * + ~ #
 *   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ
 */

/*
 * functions
 */

function urlEncodeString($string)
{
    return translateString(
        $string,
        [
            '.' => '%2E',
            '-' => '%2D',
            '_' => '%5F',
            '@' => '%40',
            '=' => '%3D',
            ')' => '%29',
            '(' => '%28',
            '/' => '%2F',
            '\\' => '%5C',
            '&' => '%26',
            '*' => '%2A',
            '+' => '%2B',
            '~' => '%7E',
            '#' => '%23',
            // used in converting to IRC compatible nicks:
            '}' => '%7D',
            '{' => '%7B',
        ]
    );
}

function ircConvertString($string)
{
    return translateString(
        $string,
        // chars/replacement allowed OC usernames and not in IRC nickname
        //   . @ ä ü ö Ä Ü Ö = ) ( / & * + ~ #
        // (adjust if additional username chars are allowed)
        [
            '.' => '',
            '@' => '{at}',
            '=' => '-',
            ')' => '}',
            '(' => '{',
            '/' => '\\',
            '&' => '',
            '*' => '',
            '+' => '',
            '~' => '-',
            '#' => '',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            '×' => 'x',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Þ' => '',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            '÷' => '',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => '',
            'ÿ' => 'y',
        ]
    );
}

function translateString($string, $translation_table)
{
    // walk through $string and encode string
    $outstring = '';
    for ($i = 0; $i < mb_strlen($string); $i ++) {
        $char = mb_substr($string, $i, 1);

        // find replacement
        if (isset($translation_table[$char])) {
            $outstring .= $translation_table[$char];
        } else {
            $outstring .= $char;
        }
    }

    // return
    return $outstring;
}
