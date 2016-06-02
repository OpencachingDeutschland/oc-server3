<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 ****************************************************************************/

namespace Oc\Frontend\Login;

use Oc\Frontend\AbstractFrontendTest;

class LoginTest extends AbstractFrontendTest
{

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @return void
     */
    public function testLoginFormOnStartPage()
    {
        $page = $this->session->getPage();
        $page->fillField('email', 'root');
        $page->fillField('password', 'developer');

        $page->pressButton('Anmelden');

        $page->clickLink('root');

        $pageTitle = $page->find('css', '.content2-pagetitle');

        self::assertEquals('Herzlich willkommen root', $pageTitle->getText());
    }
}
