<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/function.translation.php';

class TranslationTest extends AbstractModuleTest
{
    public function setUp()
    {
        $this->bootSymfonyKernel();
    }

    public function testTranslation()
    {
        global $opt;
        $opt['template']['locale'] = 'de';

        self::assertEquals(
            'Nicht gefunden',
            \smarty_function_translation(['key' => 'field_notes.log_type.2'])
        );
    }
}
