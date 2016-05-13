<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Modules;

abstract class AbstractModuleTest extends \PHPUnit_Framework_TestCase
{
    protected $dir;

    public function __construct()
    {
        parent::__construct();
        require_once(__DIR__ . '/../../htdocs/vendor/autoload.php');
        $this->dir = __DIR__;
    }
}
