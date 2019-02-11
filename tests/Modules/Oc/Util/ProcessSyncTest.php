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
    private $pidFilePath = __DIR__ . '/../../../../htdocs/var/cache2/';

    /**
     * @var ProcessSync
     */
    private $processSync;

    public function setUp(): void
    {
        $this->processSync = new ProcessSync($this->name);
        if (file_exists($this->pidFilePath . $this->name . '.pid')) {
            unlink($this->pidFilePath . $this->name . '.pid');
        }
    }

    /**
     * @group unit-tests
     */
    public function testEnterMethod(): void
    {
        self::assertTrue($this->processSync->enter());
        self::assertFalse($this->processSync->enter());
        self::assertTrue($this->processSync->leave());
    }

    /**
     * @group unit-tests
     */
    public function testCheckDaemonMethod(): void
    {
        $file = fopen($this->pidFilePath . $this->name . '.pid', 'w');
        fwrite($file, 'pid file', 100);
        fclose($file);

        self::assertFalse($this->processSync->enter());

        $file = fopen($this->pidFilePath . $this->name . '.pid', 'w');
        fwrite($file, '10000', 100);
        fclose($file);

        self::assertTrue($this->processSync->enter());
        self::assertTrue($this->processSync->leave());

        $file = fopen($this->pidFilePath . $this->name . '.pid', 'w');
        fwrite($file, '10000', 100);
        fclose($file);

        chmod($this->pidFilePath . $this->name . '.pid', 777);

        self::assertTrue($this->processSync->enter());
    }
}
