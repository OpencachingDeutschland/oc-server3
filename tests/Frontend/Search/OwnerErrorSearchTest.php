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

class OwnerErrorSearchTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-search
     *
     * @throws ElementNotFoundException if the searched element is not found
     *
     * @return void
     */
    public function testOwnerThatNotExists()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('owner', 'abecd');
        $page->pressButton('submit_owner');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        $searchResult = $page->find('css', 'td.searcherror');
        if ($searchTitle !== null && $searchResult !== null) {
            self::assertContains('0 caches matched', $searchTitle->getText());
//            self::assertContains('The user abecd doesn\'t exist.', $searchResult->getText());
        } else {
            self::fail(__METHOD__ . ' failed');
        }
    }

    public function testUserWithoutCaches()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('owner', 'öäü@');
        $page->pressButton('submit_owner');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        $searchResult = $page->find('css', 'td.searcherror');
        if ($searchTitle !== null && $searchResult !== null) {
            self::assertContains('0 caches matched', $searchTitle->getText());
//            self::assertContains('The user öäü@ does not own any caches that fit to your search options.', $searchResult->getText());
        } else {
            self::fail(__METHOD__ . ' failed');
        }
    }
}
