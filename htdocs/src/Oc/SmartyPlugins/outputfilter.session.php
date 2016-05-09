<?php
/***************************************************************************
 * You can find the license in the docs directory
 *
 * Unicode Reminder メモ
 *
 * transparent session/cookie manager
 *
 * Copyright 2007 Opencaching.de
 *
 * This looks like an unfinished experiment, the filter is not loaded anywhere.
 *
 ***************************************************************************/

function smarty_outputfilter_session($tpl_output, &$smarty)
{
    return html_insert_sid($tpl_output);
}

function html_insert_sid($text)
{
    global $opt;
    if ($opt['session']['mode'] == SAVE_SESSION) {
        if (SID != '' && session_id() != '') {
            $text = preg_replace(
                '/<\\/form>/i',
                '<input type="hidden" name="' . htmlspecialchars(session_name()) . '" value="' . htmlspecialchars(
                    session_id()
                ) . '" /></form>',
                $text
            );
            $text = preg_replace_callback(
                '/href[\s]*=[\s]*([\'"])([^\'"]*\\.php[^\'"]*)([\'"])/i',
                'html_insert_sid_callback',
                $text
            );
        }
    }

    return $text;
}

function html_insert_sid_callback($match)
{
    global $opt;

    /* match[1] = ' oder "
     * match[2] = url
     * match[3] = ' oder "
     */

    // dont add to absolute hyperlinks
    if (preg_match('/^(https?:\\/\\/|ftp:\\/\\/)/', $match[2])) {
        if (substr($match[2], 0, strlen($opt['page']['absolute_http_url'])) != $opt['page']['absolute_http_url'] &&
            substr($match[2], 0, strlen($opt['page']['absolute_https_url'])) != $opt['page']['absolute_https_url']
        ) {
            return $match[0];
        }
    }

    if (strpos($match[2], '?') === false) {
        return 'href=' . $match[1] . $match[2] . '?' . urlencode(session_name()) . '=' . urlencode(
            session_id()
        ) . $match[3];
    } else {
        return 'href=' . $match[1] . $match[2] . '&' . urlencode(session_name()) . '=' . urlencode(
            session_id()
        ) . $match[3];
    }
}
