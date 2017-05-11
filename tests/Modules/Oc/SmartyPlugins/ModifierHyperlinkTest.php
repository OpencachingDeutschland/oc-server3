<?php

namespace OcTest\Modules\Oc\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/Oc/SmartyPlugins/modifier.hyperlink.php';

class ModifierHyperlinkTest extends AbstractModuleTest
{
    public function testHyperlink()
    {
        self::assertEquals(
            '<a href="https://www.opencaching.de" alt="" target="_blank">https://www.opencaching.de</a>',
            \smarty_modifier_hyperlink('https://www.opencaching.de')
        );
    }
}
