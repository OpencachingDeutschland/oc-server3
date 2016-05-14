<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Oc\Frontend\AbstractFrontendTest;

class MyListTest extends AbstractFrontendTest
{

    public function testMyWatchesPage()
    {
        // check if issue with Undefined variable appears again ;-)
        $this->login();
        $this->session->visit($this->baseUrl . '/mylists.php');
        $page = $this->session->getPage();
        self::assertNotContains('Undefined variable', $page->getContent());
    }
}
