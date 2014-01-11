<?php

/****************************************************************************
                               _    _                _
 ___ _ __  ___ _ _  __ __ _ __| |_ (_)_ _  __ _   __| |___
/ _ \ '_ \/ -_) ' \/ _/ _` / _| ' \| | ' \/ _` |_/ _` / -_)
\___/ .__/\___|_||_\__\__,_\__|_||_|_|_||_\__, (_)__,_\___|
    |_|                                   |___/

For license information see doc/license.txt   ---   Unicode Reminder メモ

****************************************************************************/

namespace OpencachingDE\Logging;

// Keep a loose Monolog compatibility to make it easier to replace it later

class File
{
    public function __construct()
    {

    }

    public function debug($message, array $context = array())
    {

    }

    public function info($message, array $context = array())
    {

    }

    public function notice($message, array $context = array())
    {

    }

    public function warning($message, array $context = array())
    {

    }

    public function error($message, array $context = array())
    {
    }

    public function critical($message, array $context = array())
    {
    }

    public function emergency($message, array $context = array())
    {
    }
}
