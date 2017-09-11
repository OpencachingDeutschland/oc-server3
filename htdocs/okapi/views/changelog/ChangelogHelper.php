<?php

namespace okapi\views\changelog;

use ErrorException;
use Exception;
use okapi\core\Cache;
use okapi\core\Okapi;


class ChangelogHelper
{
    public $unavailable_changes = array();
    public $available_changes = array();

    public function __construct()
    {
        $cache_key = 'changelog';
        $cache_backup_key = 'changelog-backup';

        $changes_xml = Cache::get($cache_key);
        $changelog = null;

        if (!$changes_xml)
        {
            # Download the current changelog.

            try
            {
                $opts = array(
                    'http' => array(
                        'method' => "GET",
                        'timeout' => 5.0
                    )
                );
                $context = stream_context_create($opts);
                $changes_xml = file_get_contents(
                    # TODO: load from OKAPI repo
                    'https://raw.githubusercontent.com/opencaching/okapi/master/etc/changes.xml',
                    false,
                    $context
                );
                $changelog = simplexml_load_string($changes_xml);
                if (!$changelog) {
                    throw new ErrorException();
                }
                Cache::set($cache_key, $changes_xml, 3600);
                Cache::set($cache_backup_key, $changes_xml, 3600*24*30);
            }
            catch (Exception $e)
            {
                # GitHub failed on us. User backup list, if available.

                $changes_xml = Cache::get($cache_backup_key);
                if ($changes_xml) {
                    Cache::set($cache_key, $changes_xml, 3600*12);
                }
            }
        }

        if (!$changelog && $changes_xml) {
            $changelog = simplexml_load_string($changes_xml);
        }
        # TODO: verify XML scheme

        $this->unavailable_changes = array();
        $this->available_changes = array();

        if (!$changelog)
        {
            # We could not retreive the changelog from Github, and there was
            # no backup key or it expired. Probably we are on a developer
            # machine. The template will output some error message.
        }
        else
        {
            $commits = array();
            $versions = array();

            foreach ($changelog->changes->change as $change) {
                $change = array(
                    'commit' => (string)$change['commit'],
                    'version' => (string)$change['version'],
                    'time' => (string)$change['time'],
                    'type' => (string)$change['type'],
                    'comment' => trim(self::get_inner_xml($change)),
                );
                if (strlen($change['commit']) != 8
                    || $change['version'] == 0
                    || $change['time'] == ''
                    || isset($commits[$change['commit']])
                    || isset($versions[$change['version']])
                ) {
                    # All of these problems would have been detected or prevented
                    # by update_changes.

                    throw new Exception(
                        "Someone forgot to run update_changes.php (or ignored error messages)."
                    );
                } else {
                    if ($change['version'] > Okapi::$version_number) {
                        $this->unavailable_changes[] = $change;
                    } else {
                        $this->available_changes[] = $change;
                    }
                    $commits[$change['commit']] = true;
                    $versions[$change['version']] = true;
                }
            }
        }
    }

    private static function get_inner_xml($node)
    {
        /* Fetch as <some-node>content</some-node>, extract content. */

        $s = $node->asXML();
        $start = strpos($s, ">") + 1;
        $length = strlen($s) - $start - (3 + strlen($node->getName()));
        $s = substr($s, $start, $length);

        return $s;
    }
}
