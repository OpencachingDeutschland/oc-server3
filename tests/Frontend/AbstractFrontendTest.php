<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;

abstract class AbstractFrontendTest extends \PHPUnit_Framework_TestCase
{
    protected $dir;

    /** @var  GoutteDriver $driver */
    protected $driver;

    /** @var  Session $session */
    protected $session;

    public function __construct()
    {
        parent::__construct();
        require_once(__DIR__ . '/../../htdocs/vendor/autoload.php');
        $this->dir = __DIR__;
    }

    public function setUp()
    {
        parent::setUp();
        $this->driver = new GoutteDriver();
        $this->session = new Session($this->driver);
        $this->session->start();
        $this->session->visit('http://local.opencaching.de');
    }
}
