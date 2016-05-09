<?php

namespace Oc\Login;

use Oc\AbstractTest;

class LoginTest extends AbstractTest
{

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
