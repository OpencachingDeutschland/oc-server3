<?php

namespace OcTest\Modules\Oc\Util;

use Oc\Util\ProcessSync;
use OcTest\Modules\AbstractModuleTest;

class ProcessSyncTest extends AbstractModuleTest
{
    /**
     * @var string
     */
    private $name = 'test';

    /**
     * @var string
     */
    private $pidFIlePath = __DIR__ . '/../../../../htdocs/cache2/';

    /**
     * @var ProcessSync
     */
    private $processSync;

    public function setUp()
    {
        $this->processSync = new ProcessSync($this->name);
        if (file_exists($this->pidFIlePath . $this->name . '.pid')) {
            unlink($this->pidFIlePath . $this->name . '.pid');
        }
    }

    public function testEnterMethod()
    {
        self::assertTrue($this->processSync->enter());
        self::assertFalse($this->processSync->enter());
        self::assertTrue($this->processSync->leave());
    }

    public function testLeaveMethod()
    {
        self::assertFalse($this->processSync->leave('test message'));
    }

    public function testCheckDaemonMethod()
    {
        $file = fopen($this->pidFIlePath . $this->name . '.pid', 'w');
        fwrite($file, 'pid file', 100);
        fclose($file);

        self::assertFalse($this->processSync->enter());

        $file = fopen($this->pidFIlePath . $this->name . '.pid', 'w');
        fwrite($file, '10000', 100);
        fclose($file);

        self::assertTrue($this->processSync->enter());
        self::assertTrue($this->processSync->leave());

        $file = fopen($this->pidFIlePath . $this->name . '.pid', 'w');
        fwrite($file, '10000', 100);
        fclose($file);

        chmod($this->pidFIlePath . $this->name . '.pid', 000);

        self::assertFalse($this->processSync->enter());
    }
}
