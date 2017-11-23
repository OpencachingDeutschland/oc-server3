<?php
/****************************************************************************
 * For license information see LICENSE.md
 ****************************************************************************/

namespace OcTest\Modules;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class AbstractModuleTest extends PHPUnitTestCase
{
    protected $dir;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dir = __DIR__;
    }
}
