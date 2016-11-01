<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Util;

class StyleCleanUp
{
    const TABWIDTH = 4;

    /**
     * @var string
     */
    private $excludeDirs;

    /**
     * @var string
     */
    private $basedir;

    /**
     * @var string
     */
    private $filesModified;

    /**
     * @var string
     */
    private $linesModified;

    /**
     * @param string $basedir
     * @param array $excludeDirs
     */
    public function run($basedir, $excludeDirs)
    {
        $this->basedir = $basedir;
        $this->excludeDirs = $excludeDirs;
        $this->filesModified = 0;
        $this->linesModified = 0;

        $this->cleanup($basedir);
    }

    /**
     * @return string
     */
    public function getFilesModified()
    {
        return $this->filesModified;
    }

    /**
     * @return string
     */
    public function getLinesModified()
    {
        return $this->linesModified;
    }

    /**
     * @param $path
     */
    private function cleanup($path)
    {
        if (!in_array(substr($path, strlen($this->basedir) + 1), $this->excludeDirs, true)) {
            # process files in $path

            $files = array_merge(
                glob($path . '/*.php'),
                glob($path . '/*.tpl')
            );

            foreach ($files as $filePath) {
                $file_modified = false;
                $lines = file($filePath);
                $displayFilePath = substr($filePath, strlen($this->basedir) + 1);

                # detect illegal characters at start of PHP or XML file

                if (count($lines) && preg_match('/^(.+?)\<\?/', $lines[0], $matches)) {
                    self::warn(
                        'invalid character(s) "' . $matches[1] . '" at start of ' . $displayFilePath
                    );
                }

                # Remove trailing whitespaces, strip CRs, expand tabs, make
                # sure that all - including the last - line end on "\n",
                # and detect short open tags. Only-whitespace lines are
                # allowed by PSR-2 and will not be trimmed.

                $n = 1;
                foreach ($lines as &$line) {
                    if (!preg_match("/^ *(\\*|\/\/|#) *\n$/", $line)
                        && (trim($line, " \n") !== '' || substr($line, -1) !== "\n")
                    ) {
                        $oldLine = $line;
                        $line = rtrim($line);   # trims " \t\n\r\0\x0B"
                        $line = $this->expandTabs($line);
                        $line .= "\n";

                        if ($line != $oldLine) {
                            $file_modified = true;
                            ++$this->linesModified;
                        }
                    }
                    if (preg_match('/\<\?\s/', $line)) {   # relies on \n at EOL
                        self::warn('short open tag in line ' . $n . ' of ' . $displayFilePath);
                    }
                    ++$n;
                }
                unset($line);

                # remove PHP close tags and empty lines from end of file

                $l = count($lines) - 1;
                while ($l > 0) {
                    $trimmed_line = trim($lines[$l]);
                    if ($trimmed_line === '?>' || $trimmed_line === '') {
                        unset($lines[$l]);
                        $file_modified = true;
                        ++$this->linesModified;
                    } else {
                        break;
                    }
                    --$l;
                }

                if ($file_modified) {
                    echo 'cleaned ' . substr($filePath, 2) . "\n";
                    file_put_contents($filePath, implode('', $lines));
                    ++$this->filesModified;
                }
            }

            # process subdirectories in $path

            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $this->cleanup($dir);
                }
            }
        }
    }

    /**
     * @param $line
     * @return string
     */
    private static function expandTabs($line)
    {
        while (($tabPos = strpos($line, "\t")) !== false) {
            $line =
                substr($line, 0, $tabPos)
                . substr('    ', 0, self::TABWIDTH - ($tabPos % self::TABWIDTH))
                . substr($line, $tabPos + 1);
        }

        return $line;
    }

    /**
     * @param $msg
     */
    private static function warn($msg)
    {
        echo '! ' . $msg . "\n";
    }
}
