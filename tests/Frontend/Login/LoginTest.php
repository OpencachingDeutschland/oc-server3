<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Behat\Mink\Exception\ElementNotFoundException;
use Oc\Frontend\AbstractFrontendTest;

class LoginTest extends AbstractFrontendTest
{
    /**
     * @group frontend
     * @group frontend-login
     *
     * @throws ElementNotFoundException if the searched element is not found
     * @return void
     */
    public function testLoginFormOnStartPage()
    {
        $page = $this->session->getPage();
        $page->fillField('email', 'root');
        $page->fillField('password', 'developer');

        $page->pressButton('Login');

        $page->clickLink('root');

        $pageTitle = $page->find('css', '.content2-pagetitle');

        self::assertEquals('Hello root', $pageTitle->getText());
    }
}
