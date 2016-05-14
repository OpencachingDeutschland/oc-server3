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

    /** @var  string $baseUrl */
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();
        require_once(__DIR__ . '/../../htdocs/vendor/autoload.php');
        $this->dir = __DIR__;
        $this->baseUrl = 'http://local.opencaching.de';
    }

    public function setUp()
    {
        parent::setUp();
        $this->driver = new GoutteDriver();
        $this->session = new Session($this->driver);
        $this->session->start();
        $this->session->visit($this->baseUrl);
    }

    /**
     * helper method for pages which needs an active login
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    protected function login()
    {
        $page = $this->session->getPage();
        $page->fillField('email', 'root');
        $page->fillField('password', 'developer');

        $page->pressButton('Anmelden');
    }
}
