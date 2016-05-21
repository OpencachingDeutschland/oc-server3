<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  This tool currently fixes the following code style issues:
 *
 *    - resolve tabs to 4-char-columns
 *    - remove trailing whitespaces
 *    - set line ends to LF(-only)
 *    - remove ?> and blank lines at end of file
 *    - add missing LF to end of file
 *
 *  It also warns on the following issues:
 *
 *    - characters before open tag at start of file
 *    - short open tags
 *
 *  This script may be run any time to check and clean up the current OC code.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

# The following code is made to run also on the developer host, which may
# have a restricted environent like an old Windows PHP. Keep it simple and
# do not include other OC code.

$exclude = array(
    'htdocs/cache',
    'htdocs/cache2',
    'htdocs/lib2/smarty',
    'htdocs/okapi',
    'htdocs/templates2/mail',
    'htdocs/var',
    'htdocs/vendor',
);

chdir(__DIR__ . '/../..');

$cleanup = new StyleCleanup();
$cleanup->run('.', $exclude);

echo
    $cleanup->getLinesModified() . ' lines in ' . $cleanup->getFilesModified() . ' files'
    . " have been cleaned up\n";


class StyleCleanup
{
    const TABWIDTH = 4;

    private $exclude_dirs;
    private $basedir;
    private $files_modified;
    private $lines_modified;

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

            $files = array_merge(
                glob($path . '/*.php'),
                glob($path . '/*.tpl')
            );

            foreach ($files as $filepath) {
                $file_modified = false;
                $lines = file($filepath);
                $display_filepath = substr($filepath, strlen($this->basedir) + 1);

                # detect illegal characters at start of PHP or XML file

                if (count($lines) && preg_match('/^(.+?)\<\?/', $lines[0], $matches)) {
                    self::warn(
                        'invalid character(s) "' . $matches[1] . '" at start of ' . $display_filepath
                    );
                }

                # Remove trailing whitespaces, strip CRs, expand tabs, make
                # sure that all - including the last - line end on "\n",
                # and detect short open tags. Only-whitespace lines are
                # allowed by PSR-2 and will not be trimmed.

                $n = 1;
                foreach ($lines as &$line) {
                    if ((trim($line, " \n") != '' || substr($line, -1) != "\n")
                        && !preg_match("/^ *(\\*|\/\/|#) *\n$/", $line)) {

                        $old_line = $line;
                        $line = rtrim($line);   # trims " \t\n\r\0\x0B"
                        $line = $this->expandTabs($line);
                        $line .= "\n";

                        if ($line != $old_line) {
                            $file_modified = true;
                            ++ $this->lines_modified;
                        }
                    }
                    if (preg_match('/\<\?\s/', $line)) {   # relies on \n at EOL
                        self::warn('short open tag in line ' . $n . ' of ' . $display_filepath);
                    }
                    ++ $n;
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
                    echo 'cleaned ' . substr($filepath, 2) . "\n";
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
            $line =
                substr($line, 0, $tabpos)
                . substr('    ', 0, self::TABWIDTH - ($tabpos % self::TABWIDTH))
                . substr($line, $tabpos + 1);
        }

        return $line;
    }

    private static function warn($msg)
    {
        echo '! ' . $msg . "\n";
    }
}
