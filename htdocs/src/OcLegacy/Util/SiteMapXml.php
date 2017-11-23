<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 *  Generate sitemap.xml as specified by http://www.sitemaps.org
 *  And send ping to search engines
 ***************************************************************************/

namespace OcLegacy\Util;

class SiteMapXml
{
    public $sDefaultChangeFreq = 'monthly';
    public $nMaxFileSize = 9961472; // max file size, 10MB by specification
    public $nMaxUrlCount = 50000; // max number of URLs per file, 50000 by specification

    public $sPath = '';
    public $sDomain = '';
    public $oIndexFile;
    public $nSiteMapIndex = 0;
    public $oSiteMapFile;
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

        $this->oIndexFile = fopen($sPath . 'sitemap.xml', 'wb');
        if ($this->oIndexFile === false) {
            return false;
        }

        fwrite($this->oIndexFile, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($this->oIndexFile, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    }

    /* sChaneFreq = {always, hourly, daily, weekly, monthly, yearly, never}
     * nPriority  = {0.0 to 1.0}
     */

    /**
     * @param string $sFile
     * @param int $dLastMod
     * @param string $sChangeFreq
     * @param float $nPriority
     */
    public function write($sFile, $dLastMod, $sChangeFreq, $nPriority = 0.5)
    {
        if (!$sChangeFreq) {
            $sChangeFreq = $this->sDefaultChangeFreq;
        }

        $sXML = '<url>';
        $sXML .= '<loc>' . xmlentities($this->sDomain . $sFile) . '</loc>';
        $sXML .= '<lastmod>' . xmlentities(date('c', $dLastMod)) . '</lastmod>';
        $sXML .= '<changefreq>' . xmlentities($sChangeFreq) . '</changefreq>';
        $sXML .= '<priority>' . xmlentities($nPriority) . '</priority>';
        $sXML .= '</url>' . "\n";

        $this->writeInternal($sXML);
    }

    /**
     * @param string $str
     */
    public function writeInternal($str)
    {
        // close the last file?
        if (($this->oSiteMapFile) && (($this->nWrittenSize + strlen($str) > $this->nMaxFileSize) || ($this->nWrittenCount >= $this->nMaxUrlCount))) {
            gzwrite($this->oSiteMapFile, '</urlset>');
            gzclose($this->oSiteMapFile);
            $this->oSiteMapFile = null;
        }

        // open new XML file?
        if (!$this->oSiteMapFile) {
            $this->nSiteMapIndex++;
            $sFilename = 'sitemap-' . $this->nSiteMapIndex . '.xml.gz';
            $this->oSiteMapFile = gzopen($this->sPath . $sFilename, 'wb');

            fwrite(
                $this->oIndexFile,
                '<sitemap><loc>' . xmlentities($this->sDomain . $sFilename) . '</loc>' .
                '<lastmod>' . xmlentities(date('c')) . '</lastmod></sitemap>'
            );

            gzwrite($this->oSiteMapFile, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            gzwrite($this->oSiteMapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
            // includes end of xml-tag
            $this->nWrittenSize = 108;
            $this->nWrittenCount = 0;
        }

        // write string to XML
        gzwrite($this->oSiteMapFile, $str);
        $this->nWrittenSize += strlen($str);
        $this->nWrittenCount++;
    }

    public function close()
    {
        if ($this->oSiteMapFile) {
            gzwrite($this->oSiteMapFile, '</urlset>');
            gzclose($this->oSiteMapFile);
            $this->oSiteMapFile = null;
        }

        if ($this->oIndexFile !== false) {
            fwrite($this->oIndexFile, '</sitemapindex>');
            fclose($this->oIndexFile);
            $this->oIndexFile = false;
        }
    }
}
