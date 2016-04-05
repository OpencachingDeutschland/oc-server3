<?php

/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
class translate_filescan
{
    private $msFilename;
    private $msContent;
    public $textlist;

    public function __construct($sFilename)
    {
        $this->filelist = array();
        $this->msFilename = $sFilename;

        $sContent = '';
        $hFile = fopen($sFilename, 'rb');
        while (!feof($hFile)) {
            $sContent .= fread($hFile, 8192);
        }
        fclose($hFile);

        $this->msContent = $sContent;
    }

    public function parse()
    {
        $this->scanTranslationPlaceholders();
        $this->scanTranslateFunctionCalls();
    }

    public function scanTranslateFunctionCalls()
    {
        $nNextPos = strpos($this->msContent, "t" . "('");
        $nNext_ = strpos($this->msContent, "_" . "('");
        if ($nNext_ !== false && ($nNextPos === false || $nNext_ < $nNextPos)) {
            $nNextPos = $nNext_;
        }
        while ($nNextPos !== false) {
            // check for match
            $bMatch = false;
            if (substr($this->msContent, $nNextPos - 12, 12) == '$translate->') {
                $bMatch = true;
            } else {
                if ($nNextPos == 0) {
                    $sPrevChar = ' ';
                } else {
                    $sPrevChar = substr($this->msContent, $nNextPos - 1, 1);
                }

                if (preg_match('/^[a-zA-Z0-9_]$/', $sPrevChar) == 0) {
                    $bMatch = true;
                }
            }

            if ($bMatch == true) {
                $nEnd = $this->findEndOfPHPString($this->msContent, $nNextPos + 3);

                $nLine = $this->findLineOfPos($nNextPos);
                $sText = substr($this->msContent, $nNextPos + 3, $nEnd - $nNextPos - 3);

                $this->textlist[] = array(
                    'text' => $sText,
                    'line' => $nLine
                );
            }

            $nNext_ = strpos($this->msContent, "_" . "('", $nNextPos + 1);
            $nNextPos = strpos($this->msContent, "t" . "('", $nNextPos + 1);
            if ($nNext_ !== false && ($nNextPos === false || $nNext_ < $nNextPos)) {
                $nNextPos = $nNext_;
            }
        }
    }

    public function findEndOfPHPString($sCode, $nStartSearch)
    {
        $nEnd = 0;
        while ($nEnd == 0) {
            $nEnd = strpos($sCode, "'", $nStartSearch);
            if (substr($sCode, $nEnd - 1, 1) == '\\') {
                $nStartSearch = $nEnd + 1;
                $nEnd = 0;
            }
        }

        return $nEnd;
    }

    public function scanTranslationPlaceholders()
    {
        $nNextPos = strpos($this->msContent, '{' . 't');
        while ($nNextPos !== false) {
            // check for match
            if ((substr($this->msContent, $nNextPos, 3) == '{' . 't}') ||
                (substr($this->msContent, $nNextPos, 3) == '{' . 't ')
            ) {
                $nStart = strpos($this->msContent, '}', $nNextPos);
                $nEnd = strpos($this->msContent, '{/t}', $nNextPos);

                $nLine = $this->findLineOfPos($nNextPos);
                $sText = substr($this->msContent, $nStart + 1, $nEnd - $nStart - 1);

                // TODO:plural
                $this->textlist[] = array(
                    'text' => $sText,
                    'line' => $nLine
                );
            }

            $nNextPos = strpos($this->msContent, '{' . 't', $nNextPos + 1);
        }
    }

    // TODO: performance ... scan once at __construct and store line positions
    public function findLineOfPos($nPos)
    {
        $nLine = 1;

        for ($n = 0; $n < $nPos; $n ++) {
            if (substr($this->msContent, $n, 1) == "\n") {
                $nLine ++;
            }
        }

        return $nLine;
    }
}
