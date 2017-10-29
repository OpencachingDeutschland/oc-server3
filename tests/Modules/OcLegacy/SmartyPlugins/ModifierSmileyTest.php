<?php

namespace OcTest\Modules\OcLegacy\SmartyPlugins;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../../htdocs/src/OcLegacy/SmartyPlugins/modifier.smiley.php';

class ModifierSmileyTest extends AbstractModuleTest
{
    public function testSmiley()
    {
        self::assertEquals(
            ' <img src="smiley-smile.gif" alt=" :) " border="0" width="18px" height="18px" /> ',
            \smarty_modifier_smiley(' :) ')
        );

        self::assertEquals(
            ' <img src="smiley-smile.gif" alt=" :-) " border="0" width="18px" height="18px" /> ',
            \smarty_modifier_smiley(' :-) ')
        );

        self::assertEquals(
            ' <img src="smiley-foot-in-mouth.gif" alt=" :-! " border="0" width="18px" height="18px" /> ',
            \smarty_modifier_smiley(' :-! ')
        );
    }
}
