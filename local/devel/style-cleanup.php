<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Fix some common coding style issues. All changes comply to PSR-2.
 *  This script may be run any time to check and clean up the current OC code.
 *
 *  DO NOT EXPAND TABS YET. This must be done when there are no open feature
 *  branches.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$exclude = [
    'htdocs/cache',
    'htdocs/cache2',
    'htdocs/lib2/HTMLPurifier',
    'htdocs/lib2/smarty',
    'htdocs/okapi',
    'htdocs/var'
];

$expand_tabs = in_array('--tabs', $argv);

chdir(__DIR__ . '/../..');

$cleanup = new StyleCleanup();
$cleanup->setExpandTabs($expand_tabs);
$cleanup->run('.', $exclude);

echo $cleanup->getLinesModified() . ' lines in ' . $cleanup->getFilesModified() . ' files'
    . " have been cleaned up\n";


class StyleCleanup
{
    private $expand_tabs;
    private $exclude_dirs;
    private $basedir;
    private $files_modified;
    private $lines_modified;

    public function setExpandTabs($et)
    {
        $this->expand_tabs = $et;
    }

    public function run($basedir, $exclude_dirs)
    {
        $this->basedir = $basedir;
        $this->exclude_dirs = $exclude_dirs;
        $this->files_modified = 0;
        $this->lines_modified = 0;

        $this->cleanup($basedir);
    }

    public function getFilesModified()
    {
        return $this->files_modified;
    }

    public function getLinesModified()
    {
        return $this->lines_modified;
    }

    private function cleanup($path)
    {
        if (!in_array(substr($path, strlen($this->basedir) + 1), $this->exclude_dirs)) {
            # process files in $path

            $files = glob($path . '/*.php');
            foreach ($files as $filepath) {
                $file_modified = false;
                $lines = file($filepath);

                # Remove all trailing whitespaces, strip CRs and make sure
                # that all - including the last - line end on "\n".
                # Expand Tabs if requested.

                foreach ($lines as &$line) {
                    $trimmed_line = trim($line, " \t\r\n");
                    if ($trimmed_line != '' && $trimmed_line != '*') {
                        $old_line = $line;
                        $line = rtrim($line, " \t\r\n") . "\n";
                        if ($this->expand_tabs) {
                            $line = $this->expandTabs($line);
                        }
                        if ($line != $old_line) {
                            $file_modified = true;
                            ++ $this->lines_modified;
                        }
                    }
                }

                # remove PHP close tags and empty lines from end of file

                $l = count($lines) - 1;
                while ($l > 0) {
                    $trimmed_line = trim($lines[$l]);
                    if ($trimmed_line == '?>' || $trimmed_line == '') {
                        unset($lines[$l]);
                        $file_modified = true;
                        ++ $this->lines_modified;
                    } else {
                        break;
                    }
                    -- $l;
                }

                if ($file_modified) {
                    echo substr($filepath, 2) . "\n";
                    file_put_contents($filepath, implode('', $lines));
                    ++ $this->files_modified;
                }
            }

            # process subdirectories in $path

            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if ($dir != '.' && $dir != '..') {
                    $this->cleanup($dir);
                }
            }
        }
    }

    private static function expandTabs($line)
    {
        while (($tabpos = strpos($line, "\t")) !== false) {
            $line = substr($line, 0, $tabpos)
                . substr('    ', 0, 4 - ($tabpos % 4))
                . substr($line, $tabpos + 1);
        }

        return $line;
    }
}
