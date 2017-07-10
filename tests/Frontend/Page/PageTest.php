<?php
/****************************************************************************
 * For license information see doc/license.txt
 ****************************************************************************/

namespace OcTest\Frontend\Page;

use OcTest\Frontend\AbstractFrontendTest;

/**
 * Class MyListTest
 *
 * @package OcTest\Frontend\Page
 */
class MyListTest extends AbstractFrontendTest
{
    /**
     * Test that the page impressum contains the specific title.
     *
     * @return void
     */
    public function testPageImpressum()
    {
        $this->session->visit($this->baseUrl . '/page/impressum');
        $page = $this->session->getPage();

        self::assertEquals(
            'Impressum',
            $page->find('css', '#ocmain > div:nth-child(2) > div.content2-pagetitle')->getText()
        );
    }

    /**
     * Test if a page is not found.
     *
     * @return void
     */
    public function testPageNotFound()
    {
        $this->session->visit($this->baseUrl . '/page/notfound');

        self::assertEquals(
            '404',
            $this->session->getStatusCode()
        );
    }
}
