<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

/**
 * @param $eMail
 * @return int
 */
function is_valid_email_address($eMail)
{
    return (int) ($eMail === filter_var($eMail, FILTER_VALIDATE_EMAIL));
}

/**
 * @param $str
 * @return string
 */
function mb_trim($str)
{
    $bLoop = true;
    while ($bLoop === true) {
        $sPos = mb_substr($str, 0, 1);

        if ($sPos === ' ' || $sPos === "\r" || $sPos === "\n" || $sPos === "\t" || $sPos === "\x0B" || $sPos === "\0") {
            $str = mb_substr($str, 1, mb_strlen($str) - 1);
        } else {
            $bLoop = false;
        }
    }

    $bLoop = true;
    while ($bLoop === true) {
        $sPos = mb_substr($str, -1, 1);

        if ($sPos === ' ' || $sPos === "\r" || $sPos === "\n" || $sPos === "\t" || $sPos === "\x0B" || $sPos === "\0") {
            $str = mb_substr($str, 0, mb_strlen($str) - 1);
        } else {
            $bLoop = false;
        }
    }

    return $str;
}

/**
 * explode with more than one separator
 *
 * @param $str
 * @param $sep
 * @return array
 */
function explode_multi($str, $sep)
{
    $ret = [];
    $nCurPos = 0;

    while ($nCurPos < mb_strlen($str)) {
        $nNextSep = mb_strlen($str);
        $sepLength = mb_strlen($sep);
        for ($nSepPos = 0; $nSepPos < $sepLength; $nSepPos++) {
            $nThisPos = mb_strpos($str, mb_substr($sep, $nSepPos, 1), $nCurPos);
            if ($nThisPos !== false && $nNextSep > $nThisPos) {
                $nNextSep = $nThisPos;
            }
        }

        $ret[] = mb_substr($str, $nCurPos, $nNextSep - $nCurPos);

        $nCurPos = $nNextSep + 1;
    }

    return $ret;
}

/**
 * @param string $name
 * @param string $default
 * @return string
 */
function getSysConfig($name, $default)
{
    return sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='&1'", $default, $name);
}

/**
 * @param string $name
 * @param string $value
 */
function setSysConfig($name, $value)
{
    sql(
        "INSERT INTO `sysconfig` (`name`, `value`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `value`='&2'",
        $name,
        $value
    );
}

/**
 * @param $filename
 * @param $maxLength
 * @return bool|string
 */
function read_file($filename, $maxLength = 4096)
{
    $content = '';

    $f = fopen($filename, 'r');
    if ($f === false) {
        return false;
    }

    while ($line = fread($f, $maxLength)) {
        $content .= $line;
    }
    fclose($f);

    return $content;
}

/**
 * encodes &<>"'
 *
 * @param $str
 * @return string
 */
function xmlentities($str)
{
    return htmlspecialchars(xmlfilterevilchars($str), ENT_QUOTES, 'UTF-8');
}

//
/**
 * encodes &<>
 * This is ok for XML content text between tags, but not for XML attribute contents.
 *
 * @param $str
 * @return string
 */
function text_xmlentities($str)
{
    return htmlspecialchars(xmlfilterevilchars($str), ENT_NOQUOTES, 'UTF-8');
}

/**
 * @param $str
 * @return string
 */
function xmlfilterevilchars($str)
{
    // the same for for ISO-8859-1 and UTF-8
    // following 2016-3-1: allowed Tabs (\x{09})
    return mb_ereg_replace('[\x{00}-\x{08}\x{0B}\x{0C}\x{0E}-\x{1F}]*', '', $str);
}

/**
 * decimal longitude to string E/W hhh°mm.mmm
 *
 * @param $lon
 * @return string
 */
function help_lonToDegreeStr($lon)
{
    if ($lon < 0) {
        $retVal = 'W ';
        $lon = -$lon;
    } else {
        $retVal = 'E ';
    }

    $retVal = $retVal . sprintf('%03d', floor($lon)) . '° ';
    $lon -= floor($lon);
    $retVal = $retVal . sprintf('%06.3f', round($lon * 60, 3)) . '\'';

    return $retVal;
}

/**
 * decimal latitude to string N/S hh°mm.mmm
 *
 * @param $lat
 * @return string
 */
function help_latToDegreeStr($lat)
{
    if ($lat < 0) {
        $retVal = 'S ';
        $lat = -$lat;
    } else {
        $retVal = 'N ';
    }

    $retVal = $retVal . sprintf('%02d', floor($lat)) . '° ';
    $lat -= floor($lat);
    $retVal = $retVal . sprintf('%06.3f', round($lat * 60, 3)) . '\'';

    return $retVal;
}


/**
 * @param $text
 * @return string
 */
function escape_javascript($text)
{
    return str_replace(
        ['\'', '"', ],
        ['\\\'', '&quot;'],
        $text
    );
}


/**
 * perform str_rot13 without renaming parts in []
 *
 * @param $str
 * @return string
 */
function str_rot13_gc($str)
{
    /** @var array $delimiter */
    $delimiter[0][0] = '[';
    $delimiter[0][1] = ']';

    $retVal = '';

    while (mb_strlen($retVal) < mb_strlen($str)) {
        $nNextStart = false;
        $sNextEndChar = '';
        foreach ($delimiter as $del) {
            $nThisStart = mb_strpos($str, $del[0], mb_strlen($retVal));

            if ($nThisStart !== false) {
                if (($nNextStart > $nThisStart) || ($nNextStart === false)) {
                    $nNextStart = $nThisStart;
                    $sNextEndChar = $del[1];
                }
            }
        }

        if ($nNextStart === false) {
            $retVal .= str_rot13(mb_substr($str, mb_strlen($retVal)));
        } else {
            // crypted part
            $retVal .= str_rot13(mb_substr($str, mb_strlen($retVal), $nNextStart - mb_strlen($retVal)));

            // uncrypted part
            $nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

            if ($nNextEnd === false) {
                $retVal .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retVal));
            } else {
                $retVal .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
            }
        }
    }

    return $retVal;
}


/**
 * format number with 1000s dots
 *
 * @param $n
 * @return mixed|string
 */
function number1000($n)
{
    global $opt;

    if (isset($opt['locale'][$opt['template']['locale']]['format']['dot1000']) &&
        $opt['locale'][$opt['template']['locale']]['format']['dot1000'] === ','
    ) {
        return number_format($n);
    }

    return str_replace(',', '.', number_format($n));
}
