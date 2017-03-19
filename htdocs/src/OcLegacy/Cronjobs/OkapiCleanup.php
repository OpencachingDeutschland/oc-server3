<?php

namespace OcLegacy\Cronjobs;

class OkapiCleanup
{
    /**
     * @var string
     */
    public $name = 'okapi_cleanup';

    /**
     * @var int
     */
    public $interval = 3600;

    /**
     * @var string
     */
    public $okapiVarDir;

    /**
     * @param string $okapiVarDir
     */
    public function __construct($okapiVarDir = __DIR__.'/../../../var/okapi')
    {
        $this->okapiVarDir = $okapiVarDir;
    }

    public function run()
    {
        $files = glob($this->okapiVarDir . '/garmin*.zip');
        foreach ($files as $file) {
            // delete old download files after 24 hours; this large interval filters out any
            // timezone mismatches in file systems (e.g. on unconventional development environments)
            if (is_file($file) && (time() - filemtime($file)) > 24 * 3600) {
                unlink($file);
            }
        }
    }
}
