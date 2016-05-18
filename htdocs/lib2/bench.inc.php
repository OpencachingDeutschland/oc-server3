<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Exact time mesurement
 ***************************************************************************/

class Cbench
{
    public $start;
    public $stop;

    public function CBench()
    {
        $this->start = 0;
        $this->stop = 0;
    }

    public function getmicrotime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }

    public function start()
    {
        $this->start = $this->getmicrotime();
    }

    public function stop()
    {
        $this->stop = $this->getmicrotime();
    }

    public function diff()
    {
        $result = $this->stop - $this->start;

        return $result;
    }

    public function runTime()
    {
        $result = $this->getmicrotime() - $this->start;

        return $result;
    }
}
