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

class LoggerErrorSearchTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-search
     *
     * @throws ElementNotFoundException if the searched element is not found
     *
     * @return void
     */

    public function testLoggerThatNotExists()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('finder', 'abecd');
        $page->pressButton('submit_finder');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        $searchResult = $page->find('css', 'td.searcherror');
        if ($searchTitle !== null && $searchResult !== null) {
            self::assertContains('0 caches matched', $searchTitle->getText());
            self::assertContains('The user abecd doesn\'t exsist.', $searchResult->getText());
        } else {
            self::fail(__METHOD__ . ' failed');
        }
    }

    public function testFinderWithoutLogs()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('finder', 'nbtest5');
        $page->pressButton('submit_finder');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        $searchResult = $page->find('css', 'td.searcherror');
        if ($searchTitle !== null && $searchResult !== null) {
            self::assertContains('0 caches matched', $searchTitle->getText());
            self::assertContains('The user nbtest5 doesn\'t exsist.', $searchResult->getText());
        } else {
            self::fail(__METHOD__ . ' failed');
        }
    }
}
