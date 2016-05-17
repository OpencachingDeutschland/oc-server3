<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Generate sitemap.xml as specified by http://www.sitemaps.org
 *  And send ping to search engines
 ***************************************************************************/

checkJob(new sitemaps());

class sitemaps
{
    public $name = 'sitemaps';
    public $interval = 604800; // once a week

    public $oSitemapXML = false;

    public function run()
    {
        global $opt;
        if ($opt['cron']['sitemaps']['generate'] == true) {
            $this->oSitemapXML = new sitemapxml();
            $this->oSitemapXML->open(
                $opt['rootpath'],
                $opt['page']['https']['mode'] == HTTPS_ENFORCED ? $opt['page']['absolute_https_url'] : $opt['page']['absolute_http_url']
            );

            $this->oSitemapXML->write('index.php', time(), 'always');
            $this->write_viewacache_urls();
            $this->write_articles_urls();
            $this->write_viewlogs_urls();
            $this->write_viewprofile_urls();
            $this->oSitemapXML->write('tops.php', time() - 24 * 60 * 60, 'daily');
            $this->oSitemapXML->write('newcachesrest.php', time() - 24 * 60 * 60, 'daily');
            $this->write_newcaches_urls();
            $this->oSitemapXML->write('newlogs.php', time(), 'always');

            $this->oSitemapXML->close();

            if ($opt['cron']['sitemaps']['submit'] == true) {
                $this->ping_searchengines();
            }
        }
    }

    public function ping_searchengines()
    {
        global $opt;

        $url = urlencode(
            ($opt['page']['https']['mode'] == HTTPS_ENFORCED ? $opt['page']['absolute_https_url'] : $opt['page']['absolute_http_url']) . 'sitemap.xml'
        );

        $this->ping_searchengine('http://www.google.com/webmasters/tools/ping?sitemap=' . $url);
        $this->ping_searchengine(
            'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=USERID&url=' . $url
        );
        $this->ping_searchengine('http://submissions.ask.com/ping?sitemap=' . $url);
        $this->ping_searchengine('http://www.bing.com/webmaster/ping.aspx?siteMap=' . $url);
    }

    public function ping_searchengine($url)
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

    public function write_newcaches_urls()
    {
        $nCount = sql_value("SELECT COUNT(*) FROM `caches` WHERE `caches`.`status`=1", 0);
        $nIndex = 0;
        while ($nIndex < $nCount) {
            $this->oSitemapXML->write('newcaches.php?startat=' . $nIndex, time(), 'always');
            $nIndex += 100;
        }
    }

    public function write_viewprofile_urls()
    {
        $rs = sql("SELECT SQL_BUFFER_RESULT `user_id` FROM `user`");
        while ($r = sql_fetch_assoc($rs)) {
            $this->oSitemapXML->write('viewprofile.php?userid=' . $r['user_id'], time() - 31 * 24 * 60 * 60);
        }
        sql_free_result($rs);
    }

    public function write_viewlogs_urls()
    {
        $rs = sql(
            "SELECT SQL_BUFFER_RESULT MAX(`last_modified`) AS `d`, `cache_id` FROM `cache_logs` GROUP BY `cache_id`"
        );
        while ($r = sql_fetch_assoc($rs)) {
            $this->oSitemapXML->write('viewlogs.php?cacheid=' . $r['cache_id'], strtotime($r['d']));
        }
        sql_free_result($rs);
    }

    public function write_articles_urls()
    {
        $rs = sql("SELECT `href` FROM `sys_menu` WHERE `href` LIKE 'articles.php?page=%'");
        while ($r = sql_fetch_assoc($rs)) {
            $this->oSitemapXML->write($r['href'], time() - 31 * 24 * 60 * 60);
        }
        sql_free_result($rs);
    }

    public function write_viewacache_urls()
    {
        $rs = sql(
            "SELECT SQL_BUFFER_RESULT `caches`.`wp_oc`, `caches`.`cache_id`, `cache_desc`.`language`
                     FROM `caches`
               INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id`
                     INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
                    WHERE `cache_status`.`allow_user_view`=1"
        );
        while ($r = sql_fetch_assoc($rs)) {
            $dLastMod = sql_value(
                "SELECT MAX(`last_modified`) `last_modified` FROM
                (SELECT `listing_last_modified` AS `last_modified` FROM `caches` WHERE `cache_id` ='&1' UNION
                SELECT MAX(`last_modified`) AS `last_modified` FROM `cache_logs` WHERE `cache_id` ='&1') `tmp_result`",
                time(),
                $r['cache_id']
            );
            $this->oSitemapXML->write(
                'viewcache.php?wp=' . $r['wp_oc'] . '&desclang=' . $r['language'],
                strtotime($dLastMod)
            );
        }
        sql_free_result($rs);
    }
}
