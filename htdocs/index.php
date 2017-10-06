<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$sUserCountry = $login->getUserCountry();

// create object for "newest" information
$getNew = new getNew($sUserCountry);

$tpl->name = 'start';
$tpl->menuitem = MNU_START;

$tpl->caching = true;
$tpl->cache_lifetime = 300;
$tpl->cache_id = $sUserCountry . '|' . $opt['page']['protocol'];

if (!$tpl->is_cached()) {
    // welcome message
    if (isset($opt['page']['message'])) {
        $tpl->assign('message', $opt['page']['message']);
    } else {
        $tpl->assign('message', $translate->t('You can find everything you need to go Geocaching ...', '', '', 0));
    }

    // pictures
    $tpl->assign('pictures', LogPics::get(LogPics::FOR_STARTPAGE_GALLERY));

    // news entries
    if ($opt['news']['include'] != '') {
        /*
         * changed by bohrsty to fix error in displaying news from blog
         * requires $opt['news']['count'] in settings for number of blog-items
         */
        // get newest blog entries
        $tpl->assign('news', $getNew->feedForSmarty('blog'));
        $tpl->assign('newsfeed', $opt['news']['include']);
        $tpl->assign('extern_news', true);
    } else {
        $tpl->assign('extern_news', false);
    }

    if ($opt['forum']['url'] !== '') {
        /*
         * changed by bohrsty to add latest forum-entries using RSS-feed
         * requires $opt['forum']['count'] in settings for number of latest forum-posts
         * requires $opt['forum']['url'] in settings: RSS-feed-URL of the forum
         */
        // get newest forum posts
        $tpl->assign('forum_enabled', true);
        $tpl->assign('forum', $getNew->feedForSmarty('forum'));
        $tpl->assign('forum_url', $opt['forum']['url']);
        $tpl->assign('forum_name', $opt['forum']['name']);
    } else {
        $tpl->assign('forum_enabled', false);
    }

    // current cache and log-counters
    $tpl->assign(
        'count_hiddens',
        number1000(sql_value_slave('SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status` = 1', 0))
    );
    $tpl->assign(
        'count_founds',
        number1000(sql_value_slave('SELECT COUNT(*) AS `founds` FROM `cache_logs` WHERE `type` = 1', 0))
    );
    $tpl->assign(
        'count_users',
        number1000(
            sql_value_slave(
                'SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id`
                             FROM `cache_logs` UNION DISTINCT SELECT DISTINCT `user_id`
                             FROM `caches`) AS `t`', 0
            )
        )
    );

    // get newest events
    $tpl->assign_rs('events', $getNew->rsForSmarty('event'));
    // get total event count for all countries
    $tpl->assign(
        'total_events',
        sql_value_slave(
            'SELECT COUNT(*) FROM `caches` WHERE `type` = 6 AND `date_hidden` >= curdate() AND `status` = 1',
            0
        )
    );

    // get newest caches
    $tpl->assign_rs('newcaches', $getNew->rsForSmarty('cache'));
    // enable minimap for new caches if url is set
    if ($opt['logic']['minimapurl'] != '') {
        // get the correct api key for google maps
        $gmkey = '';
        $sHost = strtolower($_SERVER['HTTP_HOST']);
        if (isset($opt['lib']['google']['mapkey'][$sHost])) {
            $gmkey = $opt['lib']['google']['mapkey'][$sHost];
        }

        // build static maps url by inserting api key
        $url = $opt['page']['protocol'] . strstr($opt['logic']['minimapurl'], '://');
        $url = mb_ereg_replace('{gmkey}', $gmkey, $url);

        // put into template
        $tpl->assign('minimap_url', $url);
        $tpl->assign('minimap_enabled', true);
    } else {
        $tpl->assign('minimap_enabled', false);
    }

    // last 30 days' top ratings
    $tpl->assign_rs('topratings', $getNew->rsForSmarty('rating'));
    $tpl->assign('toprating_days', $getNew->ratingDays());

    // country and language parameters
    $sUserCountryName = sql_value(
        "SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`)
         FROM `countries`
         LEFT JOIN `sys_trans`
           ON `countries`.`trans_id`=`sys_trans`.`id`
         LEFT JOIN `sys_trans_text`
           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
           AND `sys_trans_text`.`lang`='&2'
         WHERE `countries`.`short`='&1'",
        '',
        $sUserCountry,
        $opt['template']['locale']
    );
    $tpl->assign('usercountry', $sUserCountryName);
    $tpl->assign('usercountryCode', $sUserCountry);
    if ($opt['template']['locale'] == $opt['page']['main_locale']) {
        $tpl->assign(
            'sections',
            [
                'news',
                'events',
                'logpics',
                'recommendations',
                'forum',
                'newcaches'
            ]
        );
    } else {
        $tpl->assign(
            'sections',
            [
                'events',
                'recommendations',
                'newcaches',
                'logpics',
                'forum',
                'news'
            ]
        );
    }
}

$tpl->display();
