<?php

namespace OcTest\Frontend\Changelog;

use OcTest\Frontend\AbstractFrontendTest;

class ChangelogControllerTest extends AbstractFrontendTest
{
    public function testIndexAction()
    {
        $this->session->visit($this->baseUrl . '/changelog');
        $page = $this->session->getPage();

        self::assertContains('Changes in oc-server 3.1', $page->getContent());
    }
}
