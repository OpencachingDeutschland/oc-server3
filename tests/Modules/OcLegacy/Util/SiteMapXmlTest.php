<?php

namespace OcTest\Modules\OcLegacy\Util;

use OcLegacy\Util\SiteMapXml;
use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/lib2/util.inc.php';

class SiteMapXmlTest extends AbstractModuleTest
{
    private $siteMapFile = __DIR__ . '/../../../../htdocs/var/cache2/sitemap.xml';

    private $siteMapFileGz = __DIR__ . '/../../../../htdocs/var/cache2/sitemap-1.xml.gz';

    private $domain = 'local.team-opencaching.de';

    public function setUp(): void
    {
        $this->deleteSiteMap();
    }

    public function tearDown(): void
    {
        $this->deleteSiteMap();
    }

    private function deleteSiteMap(): void
    {
        if (file_exists($this->siteMapFile)) {
            unlink($this->siteMapFile);
        }
        if (file_exists($this->siteMapFileGz)) {
            unlink($this->siteMapFileGz);
        }
    }

    public function test_open_creates_sitemapxml_file(): void
    {
        $siteMap = new SiteMapXml();
        $siteMap->nMaxUrlCount = 8;
        $siteMap->open(__DIR__ . '/../../../../htdocs/var/cache2', $this->domain);

        for ($i = 1; $i <= 10; $i++) {
            $siteMap->write('unittests.php', time());
        }

        $siteMap->close();

        self::assertFileExists($this->siteMapFile);
        $siteMapContent = file_get_contents($this->siteMapFile);

        self::assertFileExists($this->siteMapFileGz);
        self::assertContains(basename($this->siteMapFileGz), $siteMapContent);
    }
}
