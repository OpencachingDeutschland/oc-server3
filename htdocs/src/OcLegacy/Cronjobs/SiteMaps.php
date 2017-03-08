<?php
/***************************************************************************
 * For license information see doc/license.txt
 ***************************************************************************/

namespace OcLegacy\Cronjobs;

use OcLegacy\Util\SiteMapXml;

class SiteMaps
{
    public $name = 'sitemaps';
    public $interval = 604800; // once a week

    /**
     * @var SiteMapXml
     */
    public $oSiteMapXml;

    public function run()
    {
        global $opt;
        if ($opt['cron']['sitemaps']['generate'] === true) {
            $this->oSiteMapXml = new SiteMapXml();

            $page = $opt['page'];

            $this->oSiteMapXml->open(
                $opt['rootpath'],
                $page['https']['mode'] == HTTPS_ENFORCED ? $page['absolute_https_url'] : $page['absolute_http_url']
            );

            $this->oSiteMapXml->write('index.php', time(), 'always', 1.0);
            $this->oSiteMapXml->write('tops.php', time() - 24 * 60 * 60, 'daily', 0.5);
            $this->oSiteMapXml->write('newcachesrest.php', time() - 24 * 60 * 60, 'daily', 0.5);
            $this->writeViewGeocacheUrls();
            $this->writeNewGeocacheUrls();

            $this->oSiteMapXml->close();

            if ($opt['cron']['sitemaps']['submit'] === true) {
                $this->pingSearchEngines();
            }
        }
    }

    public function pingSearchEngines()
    {
        global $opt;

        $page = $opt['page'];
        $url = ($page['https']['mode'] == HTTPS_ENFORCED ? $page['absolute_https_url'] : $page['absolute_http_url']);

        $url = urlencode($url. 'sitemap.xml');

        $this->pingSearchEngine('http://www.google.com/webmasters/ping?sitemap=' . $url);
        $this->pingSearchEngine(
            'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=USERID&url=' . $url
        );
        $this->pingSearchEngine('http://submissions.ask.com/ping?sitemap=' . $url);
        $this->pingSearchEngine('http://www.bing.com/webmaster/ping.aspx?siteMap=' . $url);
    }

    /**
     * @param string $url
     * @return bool
     */
    public function pingSearchEngine($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);

        if (curl_errno($curl) != 0) {
            curl_close($curl);

            return false;
        }

        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($respCode != 200) {
            curl_close($curl);

            return false;
        }

        curl_close($curl);

        return true;
    }

    public function writeNewGeocacheUrls()
    {
        $nCount = sql_value('SELECT COUNT(*) FROM `caches` WHERE `caches`.`status`=1', 0);
        $nIndex = 0;
        while ($nIndex < $nCount) {
            $this->oSiteMapXml->write('newcaches.php?startat=' . $nIndex, time(), 'always', 0.7);
            $nIndex += 100;
        }
    }

    public function writeArticleUrls()
    {
        $rs = sql("SELECT `href` FROM `sys_menu` WHERE `href` LIKE 'articles.php?page=%'");
        while ($r = sql_fetch_assoc($rs)) {
            $this->oSiteMapXml->write($r['href'], time() - 31 * 24 * 60 * 60, 0.3);
        }
        sql_free_result($rs);
    }

    public function writeViewGeocacheUrls()
    {
        $rs = sql(
            'SELECT SQL_BUFFER_RESULT `caches`.`wp_oc`, `caches`.`cache_id`, `cache_desc`.`language`
             FROM `caches`
             INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id`
             INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
             WHERE `cache_status`.`allow_user_view`=1'
        );

        while ($r = sql_fetch_assoc($rs)) {
            $dLastMod = sql_value(
                "SELECT MAX(`last_modified`) `last_modified` FROM
                (SELECT `listing_last_modified` AS `last_modified` FROM `caches` WHERE `cache_id` ='&1' UNION
                SELECT MAX(`last_modified`) AS `last_modified` FROM `cache_logs` WHERE `cache_id` ='&1') `tmp_result`",
                time(),
                $r['cache_id']
            );
            $this->oSiteMapXml->write(
                'viewcache.php?wp=' . $r['wp_oc'] . '&desclang=' . $r['language'],
                strtotime($dLastMod),
                0.6
            );
        }
        sql_free_result($rs);
    }
}
