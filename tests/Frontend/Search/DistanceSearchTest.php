<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 ****************************************************************************/

namespace OcTest\Frontend\Login;

use Behat\Mink\Exception\ElementNotFoundException;
use OcTest\Frontend\AbstractFrontendTest;

class DistanceSearchTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-search
     *
     * @throws ElementNotFoundException if the searched element is not found
     *
     * @return void
     */
    public function testDistanceSearch()
    {
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('searchto', 'searchbyortplz');
        $page->fillField('distance', '5');
        $page->fillField('ortplz', '46359');
        $page->pressButton('submit_dist');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        if ($searchTitle !== null) {
            self::assertContains('9 caches matched', $searchTitle->getText());
        } else {
            self::fail(__METHOD__ . ' failed');
        }
    }
}
