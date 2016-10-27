<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace OcTest\Modules;

abstract class AbstractModuleTest extends \PHPUnit_Framework_TestCase
{
    protected $dir;

    public function __construct()
    {
        parent::__construct();
        $this->dir = __DIR__;
    }
}
