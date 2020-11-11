<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

//get the article name to display
$article = '';
$language = $opt['template']['locale'];
if (isset($_REQUEST['page']) &&
    (mb_strpos($_REQUEST['page'], '.') === false) &&
    (mb_strpos($_REQUEST['page'], '/') === false) &&
    (mb_strpos($_REQUEST['page'], '\\') === false)
) {
    $article = $_REQUEST['page'];
}

if ($article === '') {
    //no article specified
    $tpl->redirect('index.php');
} elseif (isset($_REQUEST['wiki'])) {
    $tpl->redirect(helppageurl($article));
} elseif (!file_exists($opt['stylepath'] . '/articles/' . $language . '/' . $article . '.tpl')) {
    // does article exist in default-language?
    $file = $opt['stylepath'] . '/articles/' . $opt['template']['default']['article_locale'] . '/' . $article . '.tpl';
    if (file_exists($file)) {
        $language = $opt['template']['default']['article_locale'];
    } elseif (file_exists($opt['stylepath'] . '/articles/EN/' . $article . '.tpl')) {
        $language = 'EN';
    } else {
        // use any
        $language = false;
        if ($hDir = opendir($opt['stylepath'] . '/articles/')) {
            while ((($sFile = readdir($hDir)) !== false) && ($language === false)) {
                if ($sFile != '.'
                    && $sFile != '..'
                    && is_dir($opt['stylepath'] . '/articles/' . $sFile)
                    && file_exists($opt['stylepath'] . '/articles/' . $sFile . '/' . $article . '.tpl')
                ) {
                    $language = $sFile;
                }
            }
            closedir($hDir);
        }

        //article doesn't exists
        if ($language === false) {
            $tpl->redirect('index.php');
        }
    }
}

$tpl->name = 'articles';

$tpl->caching = true;
$tpl->cache_id = 'articles|' . $language . '|' . $article;
$tpl->cache_lifetime = 43200;

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->menuitem = $connection->fetchColumn(
    'SELECT `id` FROM `sys_menu` WHERE `href`= :href LIMIT 1',
    [':href' => 'articles.php?page=' . urlencode($article)]
);
if ($tpl->menuitem == 0) {
    $tpl->redirect('index.php');
}

if (!$tpl->is_cached()) {
    $tpl->assign('article', $article);
    $tpl->assign('language', $language);

    /* prepare smarty vars for special pages ...
     */
    if ($article === 'cacheinfo') {
        require_once __DIR__ . '/lib2/logic/attribute.class.php';
        $attributes = attribute::getSelectableAttributesListArray(true);
        $tpl->assign('attributes', $attributes);
    }
}

$tpl->display();
