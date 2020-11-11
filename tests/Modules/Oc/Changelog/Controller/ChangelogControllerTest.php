<?php

namespace OcTest\Modules\Oc\Changelog\Controller;

use League\CommonMark\CommonMarkConverter;
use Oc\Changelog\Controller\ChangelogController;
use OcTest\Modules\TestCase;
use Twig\Environment;

class ChangelogControllerTest extends TestCase
{
    public function testIndexAction(): void
    {
        $markConverter = new CommonMarkConverter();
        $twigDummy = $this->createMock(Environment::class);
        $twigDummy->expects(static::once())
            ->method('render')
            ->with(
                'changelog/index.html.twig',
                $this->callback(static function ($parameter) {
                    return strpos($parameter['changelog'], 'Changes in oc-server 3.1') >= 0;
                })
            );

        $changelogController = new ChangelogController($markConverter, $twigDummy);
        $changelogController->indexAction();
    }
}
