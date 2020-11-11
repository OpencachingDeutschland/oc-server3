<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *  Exact time measurement
 ***************************************************************************/

namespace Oc\Util;

class CBench
{
    public $start;
    public $stop;

    /**
     * CBench constructor.
     */
    public function __construct()
    {
        $this->start = 0.00;
        $this->stop = 0.00;
    }

    private function getMicroTime(): float
    {
        [$uSec, $sec] = explode(' ', microtime());

        return ((float) $uSec + (float) $sec);
    }

    /**
     * start Benchmark
     */
    public function start(): void
    {
        $this->start = $this->getMicroTime();
    }

    /**
     * stop Benchmark
     */
    public function stop(): void
    {
        $this->stop = $this->getMicroTime();
    }

    /**
     * diff between stop and start value
     */
    public function diff(): float
    {
        return $this->stop - $this->start;
    }

    public function runTime(): float
    {
        return $this->getMicroTime() - $this->start;
    }
}
