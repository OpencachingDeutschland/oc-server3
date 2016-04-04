<?php

/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Generate sitemap.xml as specified by http://www.sitemaps.org
 *  And send ping to search engines
 ***************************************************************************/
class sitemapxml
{
    public $sDefaultChangeFreq = 'monthly';
    public $nMaxFileSize = 9961472; // max file size, 10MB by specification
    public $nMaxUrlCount = 50000;   // max number of URLs per file, 50000 by specification

    public $sPath = '';
    public $sDomain = '';
    public $oIndexFile = false;
    public $nSitemapIndex = 0;
    public $oSitemapFile = false;
    public $nWrittenSize = 0;
    public $nWrittenCount = 0;

    public function open($sPath, $sDomain)
    {
        if (substr($sPath, - 1, 1) != '/') {
            $sPath .= '/';
        }
        if (substr($sDomain, - 1, 1) != '/') {
            $sDomain .= '/';
        }

        $this->sPath = $sPath;
        $this->sDomain = $sDomain;

        $this->oIndexFile = fopen($sPath . 'sitemap.xml', 'w');
        if ($this->oIndexFile === false) {
            return false;
        }

        fwrite($this->oIndexFile, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($this->oIndexFile, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    }

    /* sChaneFreq = {always, hourly, daily, weekly, monthly, yearly, never}
     * nPriority  = {0.0 to 1.0}
     */
    public function write($sFile, $dLastMod, $sChangeFreq = false, $nPriority = 0.5)
    {
        if ($sChangeFreq == false) {
            $sChangeFreq = $this->sDefaultChangeFreq;
        }

        $sXML = '<url>';
        $sXML .= '<loc>' . xmlentities($this->sDomain . $sFile) . '</loc>';
        $sXML .= '<lastmod>' . xmlentities(date('c', $dLastMod)) . '</lastmod>';
        $sXML .= '<changefreq>' . xmlentities($sChangeFreq) . '</changefreq>';
        $sXML .= '<priority>' . xmlentities($nPriority) . '</priority>';
        $sXML .= '</url>';

        $this->writeInternal($sXML);
    }

    public function writeInternal($str)
    {
        global $opt;

        // close the last file?
        if (($this->oSitemapFile !== false) && (($this->nWrittenSize + strlen($str) > $this->nMaxFileSize) || ($this->nWrittenCount >= $this->nMaxUrlCount))) {
            gzwrite($this->oSitemapFile, '</urlset>');
            gzclose($this->oSitemapFile);
            $this->oSitemapFile = false;
        }

        // open new XML file?
        if ($this->oSitemapFile === false) {
            $this->nSitemapIndex ++;
            $sFilename = 'sitemap-' . $this->nSitemapIndex . '.xml.gz';
            $this->oSitemapFile = gzopen($this->sPath . $sFilename, 'wb');

            fwrite($this->oIndexFile, '<sitemap><loc>' . xmlentities($this->sDomain . $sFilename) . '</loc><lastmod>' . xmlentities(date('c')) . '</lastmod></sitemap>');

            gzwrite($this->oSitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            gzwrite($this->oSitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
            // includes end of xml-tag
            $this->nWrittenSize = 108;
            $this->nWrittenCount = 0;
        }

        // write string to XML
        gzwrite($this->oSitemapFile, $str);
        $this->nWrittenSize += strlen($str);
        $this->nWrittenCount ++;
    }

    public function close()
    {
        if ($this->oSitemapFile !== false) {
            gzwrite($this->oSitemapFile, '</urlset>');
            gzclose($this->oSitemapFile);
            $this->oSitemapFile = false;
        }

        if ($this->oIndexFile !== false) {
            fwrite($this->oIndexFile, '</sitemapindex>');
            fclose($this->oIndexFile);
            $this->oIndexFile = false;
        }
    }
}
