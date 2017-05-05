<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * TODO need new fixtures to reactivate this unit test
 *
 ****************************************************************************/

namespace OcTest\Frontend\Login;

use Behat\Mink\Exception\ElementNotFoundException;
use OcTest\Frontend\AbstractFrontendTest;

class WayPointSearchTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-search
     *
     * @throws ElementNotFoundException if element userinput is not found
     *
     * @return void
     */
    public function testOcWayPointSearch()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('userinput', 'OC58B4');
        $page->pressButton('Go');
        $cacheTitle = $page->find('css', '#cache_name_block');
        if ($cacheTitle !== null) {
            self::assertContains('Heiden: Nordick', $cacheTitle->getText());
        } else {
//            self::fail(__METHOD__ . ' failed');
        }
    }

    /**
     * @group frontend
     * @group frontend-search
     *
     * @throws ElementNotFoundException if element userinput is not found
     *
     * @return void
     */
    public function testInvalidOcWayPointSearch()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('userinput', 'asdf');
        $page->pressButton('Go');
        $pageTitle = $page->find('css', '.content2-pagetitle');
        if ($pageTitle !== null) {
            self::assertContains('An error occured while processing the page', $pageTitle->getText());
        } else {
//            self::fail(__METHOD__ . ' failed');
        }
    }
}
