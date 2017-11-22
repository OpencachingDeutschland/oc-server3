<?php
/****************************************************************************
 * For license information see LICENSE.md
 ****************************************************************************/

namespace OcTest\Frontend\Login;

use OcTest\Frontend\AbstractFrontendTest;

class MyListTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-account
     */
    public function testMyWatchesPage()
    {
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/mylists.php');
        $page = $this->session->getPage();
        self::assertNotContains('Undefined variable', $page->getContent());
    }
}
