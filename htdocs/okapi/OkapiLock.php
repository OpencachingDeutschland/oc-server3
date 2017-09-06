<?php

namespace okapi;

class OkapiLock
{
    private $lockfile;
    private $lock;

    /** Note: This does NOT tell you if someone currently locked it! */
    public static function exists($name)
    {
        $lockFile = Okapi::get_var_dir()."/okapi-lock-".$name;
        return file_exists($lockFile);
    }

    public static function get($name)
    {
        return new OkapiLock($name);
    }

    private function __construct($name)
    {
        if (Settings::get('DEBUG_PREVENT_SEMAPHORES'))
        {
            # Using semaphores is forbidden on this server by its admin.
            # This is possible only on development environment.
            $this->lock = null;
        }
        else
        {
            $this->lockfile = Okapi::get_var_dir()."/okapi-lock-".$name;
            $this->lock = fopen($this->lockfile, "wb");
        }
    }

    public function acquire()
    {
        if ($this->lock !== null)
            flock($this->lock, LOCK_EX);
    }

    public function try_acquire()
    {
        if ($this->lock !== null)
            return flock($this->lock, LOCK_EX | LOCK_NB);
        else
            return true;  # $lock can be null only when debugging
    }

    public function release()
    {
        if ($this->lock !== null)
            flock($this->lock, LOCK_UN);
    }

    /**
     * Use this method clean up obsolete and *unused* lock names (usually there
     * is no point in removing locks that can be reused.
     */
    public function remove()
    {
        if ($this->lock !== null)
        {
            fclose($this->lock);
            unlink($this->lockfile);
        }
    }
}
