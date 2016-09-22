<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * synchronization of processes which must not run concurrently;
 ****************************************************************************/

namespace Oc\Util;

/**
 * Class ProcessSync
 */
class ProcessSync
{
    /** @var string $pidFilePath */
    private $pidFilePath;


    /**
     * ProcessSync constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->pidFilePath = __DIR__ . '/../../../cache2/' . $name . '.pid';
    }


    /**
     * Enter code section which must not run concurrently
     *
     * @return bool
     */
    public function enter()
    {
        if (!$this->checkDaemon()) {
            return false;
        }

        if (file_exists($this->pidFilePath)) {
            echo 'Error: PidFile (' . $this->pidFilePath . ") already present\n";

            return false;
        } else {
            if ($pidFile = @fopen($this->pidFilePath, 'w')) {
                fwrite($pidFile, posix_getpid());
                fclose($pidFile);

                return true;
            } else {
                echo "can't create PidFile " . $this->pidFilePath . "\n";

                return false;
            }
        }
    }

    /**
     * checks if other instance of process is running
     *
     * @return bool
     */
    private function checkDaemon()
    {
        if ($pidFile = @fopen($this->pidFilePath, 'r')) {
            $pidDaemon = fgets($pidFile, 20);
            fclose($pidFile);

            $pidDaemon = (int)$pidDaemon;

            // bad PID file, e.g. due to system malfunction while creating the file?
            if ($pidDaemon <= 0) {
                echo 'removing bad PidFile (' . $this->pidFilePath . ")\n";
                unlink($this->pidFilePath);

                return false;
            } // process running?
            elseif (posix_kill($pidDaemon, 0)) {
                // yes, good bye
                echo 'Error: process for ' . $this->pidFilePath . " is already running with pid=$pidDaemon\n";

                return false;
            } else {
                // no, remove pid_file
                echo 'process not running, removing old PidFile (' . $this->pidFilePath . ")\n";
                unlink($this->pidFilePath);

                return true;
            }
        } else {
            return true;
        }
    }


    /**
     * Leave code section which must not run concurrently
     *
     * @param bool $message
     */
    public function leave($message = false)
    {
        if ($pidFile = @fopen($this->pidFilePath, 'r')) {
            $pid = fgets($pidFile, 20);
            fclose($pidFile);
            if ($pid === posix_getpid()) {
                unlink($this->pidFilePath);
            }
        } else {
            echo "Error: can't delete own PidFile (" . $this->pidFilePath . ")\n";
        }

        if ($message) {
            echo $message . "\n";
        }
    }
}
