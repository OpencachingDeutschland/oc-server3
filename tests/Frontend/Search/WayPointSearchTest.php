<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Behat\Mink\Exception\ElementNotFoundException;
use Oc\Frontend\AbstractFrontendTest;

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
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('userinput', 'OC58B4');
        $page->pressButton('Go');
        $cacheTitle = $page->find('css', '#cache_name_block');
        self::assertContains('Heiden: Nordick', $cacheTitle->getText());
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
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('userinput', 'asdf');
        $page->pressButton('Go');
        $pageTitle = $page->find('css', '.content2-pagetitle');
        self::assertContains('Beim Aufruf der Seite ist ein Fehler aufgetreten.', $pageTitle->getText());
    }
}
