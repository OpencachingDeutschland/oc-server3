<?php

namespace OcTest\Frontend\Changelog;

use League\CommonMark\CommonMarkConverter;
use Oc\Changelog\Controller\ChangelogController;
use OcTest\Modules\TestCase;
use OcTest\Utils\TwigDummy;

class ChangelogControllerTest extends TestCase
{
    public function testIndexAction()
    {
        $markConverter = new CommonMarkConverter();
        $twigDummy = new TwigDummy();

        $changelogController = new ChangelogController($markConverter, $twigDummy);

        self::assertContains('Changes in oc-server 3.1', $changelogController->indexAction()->getContent());
    }
}
