<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Behat\Mink\Exception\ElementNotFoundException;
use Oc\Frontend\AbstractFrontendTest;

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
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/search.php');
        $page = $this->session->getPage();
        $page->fillField('searchto', 'searchbyortplz');
        $page->fillField('distance', '5');
        $page->fillField('ortplz', '46359');
        $page->pressButton('submit_dist');
        $searchTitle = $page->find('css', 'p.content-title-noshade-size15');
        self::assertContains('9 Caches gefunden', $searchTitle->getText());
    }
}
