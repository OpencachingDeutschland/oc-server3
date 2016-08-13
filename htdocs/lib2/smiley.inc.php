<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Smiley translater for smarty extension, see modifier_smiley.php.
 *  Same content like smilies.class.php.
 ***************************************************************************/

global $smiley;
global $opt;

if (!isset($smiley)) {
    $smiley['file'] = [
        'smiley-smile.gif',
        'smiley-smile.gif',
        'smiley-wink.gif',
        'smiley-wink.gif',
        'smiley-laughing.gif',
        'smiley-cool.gif',
        'smiley-innocent.gif',
        'smiley-surprised.gif',
        'smiley-surprised.gif',
        'smiley-frown.gif',
        'smiley-frown.gif',
        'smiley-embarassed.gif',
        'smiley-cry.gif',
        'smiley-kiss.gif',
        'smiley-tongue-out.gif',
        'smiley-tongue-out.gif',
        'smiley-undecided.gif',
        'smiley-undecided.gif',
        'smiley-yell.gif',
        'smiley-foot-in-mouth.gif',
        'smiley-money-mouth.gif',
        'smiley-sealed.gif',
    ];

    $smiley['text'] = [
        " :) ",
        " :-) ",
        " ;) ",
        " ;-) ",
        " :D ",
        " 8) ",
        " O:) ",
        " :-o ",
        " :o ",
        " :( ",
        " :-( ",
        " ::| ",
        " :,-( ",
        " :-* ",
        " :P ",
        " :-P ",
        " :-/ ",
        " :/ ",
        " XO ",
        " :-! ",
        " :-($) ",
        " :x ",
    ];

    // This array currently is not used in lib2 code.
    $smiley['show'] = [
        '1',
        '0',
        '1',
        '0',
        '1',
        '1',
        '1',
        '0',
        '1',
        '1',
        '0',
        '1',
        '1',
        '1',
        '1',
        '0',
        '0',
        '1',
        '1',
        '0',
        '0',
        '0',
    ];

    $smiley_a = array();
    for ($n = 0; $n < count($smiley['file']); ++ $n) {
        $smiley['image'][$n] = '<img src="' . $opt['template']['smiley'] . $smiley['file'][$n] . '" alt="' . $smiley['text'][$n] . '" border="0" width="18px" height="18px" />';
        $smiley['spaced_image'][$n] = ' ' . $smiley['image'][$n] . ' ';
        $smiley_a[] = [
            'text' => $smiley['text'][$n],
            'file' => $smiley['file'][$n],
            'image' => $smiley['image'][$n],
            'show' => $smiley['show'][$n]
        ];
    }
}
