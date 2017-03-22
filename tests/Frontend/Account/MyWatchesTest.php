<?php
/****************************************************************************
 * For license information see doc/license.txt
 ****************************************************************************/

namespace OcTest\Frontend\Login;

use OcTest\Frontend\AbstractFrontendTest;

class MyWatchesTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-account
     *
     * @return void
     */
    public function testMyWatchesPage()
    {
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/mywatches.php');
        $page = $this->session->getPage();
        self::assertNotContains('Undefined variable', $page->getContent());
    }
}
