<?php
/****************************************************************************
 * common functions for text editors
 *
 ****************************************************************************/

// used in both lib1 and lib2 code

use OcLegacy\Editor\EditorConstants;

require_once __DIR__ . '/smiley.inc.php';


/**
 * Do all the conversions needed to process HTML or plain text editor input,
 * for either storing it into the database or (when swiching modes)
 * re-displaying it in another editor mode.
 *
 * oldDescMode is the mode in which the editor was running which output the $text,
 * or 0 if the text came from the database with `htm_text` = 0.
 *
 * descMode    is == descMode if the user hit the editor's "save" button,
 * or the new mode if the user hit another mode button
 * @param mixed $oldDescMode
 * @param mixed $descMode
 * @param mixed $text
 * @param & $representText
 */

/**
 * @param $oldDescMode
 * @param $descMode
 * @param $text
 * @return mixed|string
 */
function processEditorInput($oldDescMode, $descMode, $text, &$representText)
{
    global $opt;

    // save HTML input => verify / tidy / filter;
    // also implemented in okapi/services/logs/submit.php
    $purifier = new OcHTMLPurifier($opt);
    $text = $purifier->purify($text);
    $representText = $text;

    return $text;
}


// $texthtml0 is set if the text is from cache_desc.desc or cache_logs.text
// and text_html is 0, i.e. the text was edited in the "text" editor mode.
//
// If $wrap is > 0, longer lines will be wrapped to new lines.

/**
 * @param $text
 * @param $texthtml0
 *
 * @return mixed|string
 */
function html2plaintext($text, $texthtml0)
{
    global $smiley;

    if ($texthtml0) {
        $text = str_replace(
            [
                '<p>',
                "\n",
                "\r",
            ],
            '',
            $text
        );
        $text = str_replace(
            [
                '<br />',
                '</p>',
            ],
            "\n",
            $text
        );
        $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
    } else {
        // convert smileys ...
        $countSmileyImage = count($smiley['image']);
        for ($n = 0; $n < $countSmileyImage; $n++) {
            $text = mb_ereg_replace(
                '<img [^>]*?src=[^>]+?' . str_replace('.', '\.', $smiley['file'][$n]) . '[^>]+?>',
                '[s![' . $smiley['text'][$n] . ']!s]',
                $text
            );
            // the [s[ ]s] is needed to protect the spaces around the smileys
        }

        // REDMINE-1249: Missing log text in mail notification
        // simpler solution that converts html to text as the previous class html2text emptied the text completely
        // implementation for line wrap, url's and probably more is missing
        $text = preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($text))));

        $text = str_replace(
            [
                '[s![',
                ']!s]',
            ],
            '',
            $text
        );
    }

    return $text;
}


/**
 * @return string
 */
function editorJsPath()
{
    return 'resource2/ocstyle/js/editor.js?ft=' . filemtime('resource2/ocstyle/js/editor.js');
}
