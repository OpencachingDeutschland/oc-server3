<?php

/****************************************************************************
                               _    _                _
 ___ _ __  ___ _ _  __ __ _ __| |_ (_)_ _  __ _   __| |___
/ _ \ '_ \/ -_) ' \/ _/ _` / _| ' \| | ' \/ _` |_/ _` / -_)
\___/ .__/\___|_||_\__\__,_\__|_||_|_|_||_\__, (_)__,_\___|
    |_|                                   |___/

For license information see doc/license.txt   ---   Unicode Reminder メモ

****************************************************************************/

namespace OpencachingDE\Config;

class Config
{
    public function __construct()
    {
        $logger = $this->getContainer()->get('logger');
        $logger->debug('Hello world!');
    }

    public function getContainer()
    {
        return $GLOBALS['container'];
    }
}
