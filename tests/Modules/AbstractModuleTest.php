<?php
/****************************************************************************
 * For license information see LICENSE.md
 ****************************************************************************/

namespace OcTest\Modules;

use PHPUnit\Framework\TestCase;

abstract class AbstractModuleTest extends TestCase
{
    protected $dir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dir = __DIR__;
    }
}
