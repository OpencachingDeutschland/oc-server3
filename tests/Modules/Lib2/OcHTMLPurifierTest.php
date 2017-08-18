<?php

namespace OcTest\Modules\Lib2;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../htdocs/lib2/util.inc.php';

class OcHTMLPurifierTest extends AbstractModuleTest
{
    /**
     * @var \OcHTMLPurifier
     */
    private $htmlPurifier;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        global $opt;
        $this->htmlPurifier = new \OcHTMLPurifier($opt);
    }

    public function testArticleTagIsRemoved()
    {
        $newString = $this->htmlPurifier->purify('<article>lorem ipsum</article>');
        self::assertEquals('lorem ipsum', $newString);
    }

    public function testAsideTagArticleIsRemoved()
    {
        $newString = $this->htmlPurifier->purify('<aside>lorem ipsum</aside>');
        self::assertEquals('lorem ipsum', $newString);
    }

    public function testStrikeTagArticleIsRemoved()
    {
        $newString = $this->htmlPurifier->purify('<strike>lorem ipsum</strike>');
        self::assertEquals('lorem ipsum', $newString);
    }

    public function testHtmlTagAbbrIsAllowed()
    {
        $string = '<abbr>lorem ipsum</abbr>';
        $newString = $this->htmlPurifier->purify($string);
        self::assertEquals($string, $newString);
    }

    public function testHtmlTagAIsAllowed()
    {
        $string = '<a href="http://www.google.de" target="_blank">lorem ipsum</a>';
        $newString = $this->htmlPurifier->purify($string);
        self::assertEquals($string, $newString);
    }
}
