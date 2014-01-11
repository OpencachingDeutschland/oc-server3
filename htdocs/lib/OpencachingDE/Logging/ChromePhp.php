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

class ChromePhp
{
    public function __construct()
    {
    }

    public function debug($message, array $context = array())
    {
        \ChromePhp::log($message);
    }

    public function info($message, array $context = array())
    {
        \ChromePhp::info($message);
    }

    public function notice($message, array $context = array())
    {
        \ChromePhp::info($message);
    }

    public function warning($message, array $context = array())
    {
        \ChromePhp::warn($message);
    }

    public function error($message, array $context = array())
    {
        \ChromePhp::error($message);
    }

    public function critical($message, array $context = array())
    {
        \ChromePhp::error($message);
    }

    public function emergency($message, array $context = array())
    {
        \ChromePhp::error($message);
    }
}
