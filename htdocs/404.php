<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$sUserCountry = $login->getUserCountry();

// create object for "newest" information
$getNew = new getNew($sUserCountry);

$tpl->main_template = 'sys_oc404';
$tpl->name = 'sys_oc404';

$tpl->caching = true;
$tpl->cache_lifetime = 300;
$tpl->cache_id = $sUserCountry;

// test for redirection to this page
$isRedirect404 = isset($_SERVER['REDIRECT_URL']);
$tpl->assign('isRedirect404', $isRedirect404);

// determine website url, if is 404 redirection
if ($isRedirect404) {
    // check length
    $uril = 70;
    $uri = 'http://' . strtolower($_SERVER['SERVER_NAME']) . $_SERVER['REQUEST_URI'];
    // limit to $uril
    if (strlen($uri) > $uril) {
        $uri = substr($uri, 0, $uril - 3) . '...';
    }

    // assign uri
    $tpl->assign('website', $uri);
} else {
    $tpl->assign('website', '');
}

// set feeds and options
$feeds = [
    'blog',
    'forum',
    'wiki'
];
$options = $feeds;
$options[] = 'newcaches';

// simplify $opt
foreach ($options as $option) {
    if (isset($opt['page']['404'][$_SERVER['SERVER_NAME']][$option])) {
        $opt404[$option] = $opt['page']['404'][$_SERVER['SERVER_NAME']][$option];
    } else {
        $opt404[$option] = $opt['page']['404']['www.opencaching.de'][$option];
    }
    if ($opt404[$option]['urlname'] == '') {
        $opt404[$option]['urlname'] = parse_url($opt404[$option]['url'], PHP_URL_HOST);
    }
}

// get feeds from $feeds array
foreach ($feeds as $feed) {
    if ($isRedirect404) {
        if ($opt404[$feed]['show']) {
            $tpl->assign($feed, $getNew->feedForSmarty($feed, 3, $opt404[$feed]['feedurl'], $opt404[$feed]['timeout']));
        }
    } else {
        $tpl->assign($feed, $getNew->feedForSmarty($feed, 3));
    }
}

// get newest caches
if ($isRedirect404) {
    if ($opt404['newcaches']['show']) {
        $tpl->assign_rs(
            'newcaches',
            $getNew->rsForSmarty(
                'cache',
                [
                    $sUserCountry,
                    $opt['template']['locale'],
                    3
                ]
            )
        );
    }
} else {
    $tpl->assign_rs(
        'newcaches',
        $getNew->rsForSmarty(
            'cache',
            [
                $sUserCountry,
                $opt['template']['locale'],
                3
            ]
        )
    );
}

// assign $opt404
$tpl->assign('opt404', $opt404);

// show page
$tpl->display();
