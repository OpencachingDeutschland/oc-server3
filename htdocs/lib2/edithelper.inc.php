<?php
/****************************************************************************
 * common functions for text editors
 *
 * Unicode Reminder メモ
 ****************************************************************************/

// used in both lib1 and lib2 code

require_once 'smiley.inc.php';


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
 */

function processEditorInput($oldDescMode, $descMode, $text)
{
    global $opt, $smiley;

    if ($descMode != 1) {
        if ($oldDescMode == 1) {
            // mode switch from plain text to HTML editor => convert HTML special chars
            $text = nl2br(htmlspecialchars($text));
            // .. and smilies
            $text = str_replace($smiley['text'], $smiley['spaced_image'], $text);
        } else {
            // save HTML input => verify / tidy / filter;
            // also implemented in okapi/services/logs/submit.php
            $purifier = new OcHTMLPurifier($opt);
            $text = $purifier->purify($text);
        }
    } else {
        if ($oldDescMode == 1) {
            // save plain text input => convert to HTML;
            // also implemented in okapi/services/logs/submit.php
            $text = nl2br(htmlspecialchars($text, ENT_COMPAT, 'UTF-8'));
        } else {
            // mode switch from HTML editor to plain text, or decode HTML-encoded plain text
            $text = html2plaintext($text, $oldDescMode = 0, 0);
        }
    }

    return $text;
}


// $texthtml0 is set if the text is from cache_desc.desc or cache_logs.text
// and text_html is 0, i.e. the text was edited in the "text" editor mode.
//
// If $wrap is > 0, longer lines will be wrapped to new lines.

function html2plaintext($text, $texthtml0, $wrap)
{
    global $opt, $smiley;

    if ($texthtml0) {
        $text = str_replace(
            [
                '<p>',
                "\n",
                "\r"
            ],
            '',
            $text
        );
        $text = str_replace(
            [
                '<br />',
                '</p>'
            ],
            "\n",
            $text
        );
        $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
    } else {
        // convert smilies ...
        for ($n = 0; $n < count($smiley['image']); $n ++) {
            $text = mb_ereg_replace(
                "<img [^>]*?src=[^>]+?" . str_replace('.', '\.', $smiley['file'][$n]) . "[^>]+?>",
                "[s![" . $smiley['text'][$n] . "]!s]",
                $text
            );
            // the [s[ ]s] is needed to protect the spaces around the smileys
        }

        $h2t = new html2text($text);
        $h2t->set_base_url($opt['page']['default_absolute_url']);
        $h2t->width = $wrap;
        $text = $h2t->get_text();

        $text = str_replace(
            [
                '[s![',
                ']!s]'
            ],
            '',
            $text
        );

        // remove e.g. trailing \n created from </p> by html2text
        while (substr($text, - 2) == "\n\n") {
            $text = substr($text, 0, strlen($text) - 1);
        }
    }

    return $text;
}


function editorJsPath()
{
    return 'resource2/ocstyle/js/editor.js?ft=' . filemtime('resource2/ocstyle/js/editor.js');
}
