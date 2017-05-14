<?php

namespace OcTest\Modules\Lib2;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../htdocs/lib2/util.inc.php';

class UtilIncTest extends AbstractModuleTest
{
    public function testNumber1000()
    {
        global $opt;
        $opt['template']['locale'] = 'de';
        $opt['locale'][$opt['template']['locale']]['format']['dot1000'] = ',';

        self::assertEquals('1,000', \number1000(1000));

        $opt['locale'][$opt['template']['locale']]['format']['dot1000'] = '.';

        self::assertEquals('1.000', \number1000(1000));
    }

    public function testStrRot13Gc()
    {
        $string = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr,[sed]  diam';
        $result = 'Yberz vcfhz qbybe fvg nzrg, pbafrgrghe fnqvcfpvat ryvge,[sed]  qvnz';
        self::assertEquals($result, \str_rot13_gc($string));

        $string = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr,[sed]  [diam';
        $result = 'Yberz vcfhz qbybe fvg nzrg, pbafrgrghe fnqvcfpvat ryvge,[sed]  [diam';
        self::assertEquals($result, \str_rot13_gc($string));
    }

    public function testEscapeJavascript()
    {
        self::assertEquals(
            '&quot;',
            \escape_javascript('"')
        );

        self::assertEquals(
            '\\',
            \escape_javascript('\\')
        );

        self::assertEquals(
            '\\\'',
            \escape_javascript('\'')
        );
    }

    public function testIsValidEmailAddress()
    {
        self::assertTrue((bool) \is_valid_email_address('example@test.com'));


        self::assertFalse((bool) \is_valid_email_address('example'));
        self::assertFalse((bool) \is_valid_email_address('ääää1230@test.de'));
        self::assertFalse((bool) \is_valid_email_address('!"/()!)"@test.de'));
        self::assertFalse((bool) \is_valid_email_address('example@test'));
    }
}
