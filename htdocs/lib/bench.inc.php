<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

//
// bench.inc.php
//
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
        return $this->stop - $this->start;
    }

    public function runTime()
    {
        return $this->getmicrotime() - $this->start;
    }
}
