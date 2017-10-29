<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.hyperlink.php';

class ModifierHyperlinkTest extends AbstractModuleTest
{
    public function testHyperlink()
    {
        self::assertEquals(
            '<a href="https://www.opencaching.de" alt="" target="_blank">https://www.opencaching.de</a>',
            \smarty_modifier_hyperlink('https://www.opencaching.de')
        );

        self::assertEquals(
            '<a href="http://www.opencaching.de" alt="" target="_blank">http://www.opencaching.de</a>',
            \smarty_modifier_hyperlink('http://www.opencaching.de')
        );
    }
}
