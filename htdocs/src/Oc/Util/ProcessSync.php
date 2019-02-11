<?php
/****************************************************************************
 * For license information see LICENSE.md
 * synchronization of processes which must not run concurrently;
 ****************************************************************************/

namespace Oc\Util;

class ProcessSync
{
    /**
     * @var string
     */
    private $pidFilePath;

    public function __construct(string $name)
    {
        $this->pidFilePath = __DIR__ . '/../../../var/cache2/' . $name . '.pid';
    }

    /**
     * Enter code section which must not run concurrently
     */
    public function enter(): bool
    {
        if (!$this->checkDaemon()) {
            return false;
        }

        if ($pidFile = @fopen($this->pidFilePath, 'w')) {
            fwrite($pidFile, posix_getpid());
            fclose($pidFile);

            return true;
        }

        echo "can't create PidFile " . $this->pidFilePath . "\n";

        return false;
    }

    /**
     * checks if other instance of process is running
     */
    private function checkDaemon(): bool
    {
        if (file_exists($this->pidFilePath) && $pidFile = @fopen($this->pidFilePath, 'r')) {
            $pidDaemon = fgets($pidFile, 20);
            fclose($pidFile);

            $pidDaemon = (int) $pidDaemon;

            // bad PID file, e.g. due to system malfunction while creating the file?
            if ($pidDaemon <= 0) {
                echo 'removing bad PidFile (' . $this->pidFilePath . ")\n";
                unlink($this->pidFilePath);

                return false;
            }

            if (posix_kill($pidDaemon, 0)) { // process running?
                // yes, good bye
                echo 'Error: process for ' . $this->pidFilePath . " is already running with pid=$pidDaemon\n";

                return false;
            }
            // no, remove pid_file
            echo 'process not running, removing old PidFile (' . $this->pidFilePath . ")\n";
            unlink($this->pidFilePath);

            return true;
        }

        return true;
    }

    /**
     * Leave code section which must not run concurrently
     */
    public function leave(bool $message = false): bool
    {
        if ($message) {
            echo $message . "\n";
        }

        if ($pidFile = @fopen($this->pidFilePath, 'r')) {
            $pid = fgets($pidFile, 20);
            fclose($pidFile);
            if ($pid == posix_getpid()) {
                unlink($this->pidFilePath);
            }

            return true;
        }

        echo "Error: can't delete own PidFile (" . $this->pidFilePath . ")\n";

        return false;
    }
}
