<?php
/****************************************************************************
 * For license information see LICENSE.md
 ****************************************************************************/

namespace OcTest\Frontend;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;

abstract class AbstractFrontendTest extends \PHPUnit_Framework_TestCase
{
    protected $dir;

    /** @var  GoutteDriver $driver */
    protected $driver;

    /** @var  Session $session */
    protected $session;

    /** @var  string $baseUrl */
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->dir = __DIR__;
        $this->baseUrl = getenv('URL');
    }

    public function setUp()
    {
        parent::setUp();
        $this->driver = new GoutteDriver();
        $this->session = new Session($this->driver);
        $this->session->start();
        $this->session->visit($this->baseUrl . '/index.php?locale=EN');
    }

    /**
     * helper method for pages which needs an active login
     *
     * @throws ElementNotFoundException
     *
     * @return void
     */
    protected function login()
    {
        $page = $this->session->getPage();

        $page->fillField('email', 'root');
        $page->fillField('password', 'developer');

        $page->pressButton('Login');
    }
}
