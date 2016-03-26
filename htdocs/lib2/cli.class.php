<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$cli = new cli();

class cli
{
    public function out($str)
    {
        echo $str . "\n";
    }

    public function debug($str)
    {
        global $opt;
        if (($opt['debug'] & DEBUG_CLI) == DEBUG_CLI) {
            echo 'DEBUG: ' . $str . "\n";
        }
    }

    public function warn($str)
    {
        echo 'WARN: ' . $str . "\n";
    }

    public function error($str)
    {
        echo 'ERROR: ' . $str . "\n";
    }

    public function fatal($str)
    {
        echo 'FATAL: ' . $str . "\n";
        exit;
    }
}
