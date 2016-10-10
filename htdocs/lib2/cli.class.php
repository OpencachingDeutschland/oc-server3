<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$cli = new cli();

/**
 * Class cli
 */
class cli
{
    /**
     * @param $str
     */
    public function out($str)
    {
        echo $str . "\n";
    }

    /**
     * @param $str
     */
    public function debug($str)
    {
        global $opt;
        if (($opt['debug'] & DEBUG_CLI) == DEBUG_CLI) {
            echo 'DEBUG: ' . $str . "\n";
        }
    }

    /**
     * @param $str
     */
    public function warn($str)
    {
        echo 'WARN: ' . $str . "\n";
    }

    /**
     * @param $str
     */
    public function error($str)
    {
        echo 'ERROR: ' . $str . "\n";
    }

    /**
     * @param $str
     */
    public function fatal($str)
    {
        echo 'FATAL: ' . $str . "\n";
        exit;
    }
}
