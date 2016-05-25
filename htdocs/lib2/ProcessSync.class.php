<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * synchronization of processes which must not run concurrently;
 * also used in lib1
 ****************************************************************************/

class ProcessSync
{
    public $pidfile_path;


    public function __construct($name)
    {
        global $opt;
        $this->pidfile_path = $opt['rootpath'] . "cache2/$name.pid";
    }


    // Enter code section which must not run concurrently

    public function Enter()
    {
        if (!$this->CheckDaemon()) {
            return false;
        }

        if (file_exists($this->pidfile_path)) {
            echo "Error: Pidfile (" . $this->pidfile_path . ") already present\n";

            return false;
        } else {
            if ($pidfile = @fopen($this->pidfile_path, "w")) {
                fputs($pidfile, posix_getpid());
                fclose($pidfile);

                return true;
            } else {
                echo "can't create Pidfile " . $this->pidfile_path . "\n";

                return false;
            }
        }
    }


    // checks if other instance of process is running

    private function CheckDaemon()
    {
        if ($pidfile = @fopen($this->pidfile_path, "r")) {
            $pid_daemon = fgets($pidfile, 20);
            fclose($pidfile);

            $pid_daemon = (int)$pid_daemon;

            // bad PID file, e.g. due to system malfunction while creating the file?
            if ($pid_daemon <= 0) {
                echo "removing bad pid_file (" . $this->pidfile_path . ")\n";
                unlink($this->pidfile_path);

                return false;
            } // process running?
            elseif (posix_kill($pid_daemon, 0)) {
                // yes, good bye
                echo "Error: process for " . $this->pidfile_path . " is already running with pid=$pid_daemon\n";

                return false;
            } else {
                // no, remove pid_file
                echo "process not running, removing old pid_file (" . $this->pidfile_path . ")\n";
                unlink($this->pidfile_path);

                return true;
            }
        } else {
            return true;
        }
    }


    // Leave code section which must not run concurrently

    public function Leave($message = false)
    {
        if ($pidfile = @fopen($this->pidfile_path, "r")) {
            $pid = fgets($pidfile, 20);
            fclose($pidfile);
            if ($pid == posix_getpid()) {
                unlink($this->pidfile_path);
            }
        } else {
            echo "Error: can't delete own pidfile (" . $this->pidfile_path . ")\n";
        }

        if ($message) {
            echo $message . "\n";
        }
    }
}
