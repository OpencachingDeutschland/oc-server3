<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->name = 'newcaches';
$tpl->menuitem = MNU_START_NEWCACHES;
$tpl->change_country_inpage = true;

$startAt = isset($_REQUEST['startat']) ? floor($_REQUEST['startat'] + 0) : 0;
$country = isset($_REQUEST['usercountry']) ? $_REQUEST['usercountry'] : (isset($_REQUEST['country']) ? $_REQUEST['country'] : '');
$cacheType = isset($_REQUEST['cachetype']) ? $_REQUEST['cachetype'] + 0 : 0;
$bEvents = ($cacheType == 6);

$perpage = 100;
$startAt -= $startAt % $perpage;
if ($startAt < 0) {
    $startAt = 0;
}

$tpl->caching = true;
$tpl->cache_id = $startAt . '-' . $country . '-' . $cacheType;
$tpl->cache_lifetime = 300;
if ($startAt > 10 * $perpage) {
    $tpl->cache_lifetime = 3600;
}

if (!$tpl->is_cached()) {
    $dateField = $bEvents ? 'date_hidden' : 'date_created';
    $sortOrder = $bEvents ? 'ASC' : 'DESC';

    $newCachesQuery = $connection->createQueryBuilder()
        ->select('`caches`.`cache_id` `cacheid`')
        ->addSelect('`caches`.`wp_oc` `wpoc`')
        ->addSelect('`caches`.`name` `cachename`')
        ->addSelect('`caches`.`type`')
        ->addSelect('`caches`.`country` `country`')
        ->addSelect('`caches`.`' . $dateField . '` `date_created`')
        ->addSelect('IFNULL(`sys_trans_text`.`text`')
        ->addSelect('`countries`.`en`) AS `country_name`')
        ->addSelect('`user`.`user_id` `userid`')
        ->addSelect('`user`.`username` `username`')
        ->addSelect('`ca`.`attrib_id` IS NOT NULL AS `oconly`')
        ->from('caches')
        ->innerJoin('caches', 'user', 'user', '`caches`.`user_id`=`user`.`user_id`')
        ->leftJoin('caches', 'countries', 'countries', '`countries`.`short` = `caches`.`country`')
        ->leftJoin(
            'countries',
            'sys_trans_text',
            'sys_trans_text',
            '`sys_trans_text`.`trans_id` = `countries`.`trans_id` AND `sys_trans_text`.`lang` = :language'
        )
        ->leftJoin(
            'caches',
            'caches_attributes',
            'ca',
            '`ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`= :cacheAttributeId'
        )
        ->where('`caches`.`status` = :cacheStatus')
        ->setParameters(
            [
                ':language' => $opt['template']['locale'],
                ':cacheStatus' => 1,
                ':cacheAttributeId' => 6,
            ]
        )
        ->orderBy('caches.' . $dateField, $sortOrder)
        ->setFirstResult((int) $startAt)
        ->setMaxResults((int) $perpage);

    if ($country) {
        $newCachesQuery->andWhere('`caches`.`country`= :country');
        $newCachesQuery->setParameter('country', $country);
    }

    if ($cacheType) {
        $newCachesQuery->andWhere('`caches`.`type` = :cacheType');
        $newCachesQuery->setParameter(':cacheType', $cacheType);
    }
    if ($bEvents) {
        $newCachesQuery->andWhere('`date_hidden` >= curdate()');
    }

    $newCaches = $newCachesQuery->execute()->fetchAll();

    $tpl->assign('newCaches', $newCaches);

    $startAt = isset($_REQUEST['startat']) ? $_REQUEST['startat'] + 0 : 0;
    $cacheype_par = ($cacheType ? "&cachetype=$cacheType" : '');

    $countQuery = $connection->createQueryBuilder()
        ->select('COUNT(*)')
        ->from('caches')
        ->where('caches.status = :statusId')
        ->setParameter(':statusId', 1);

    if ($country === '') {
        if ($cacheType) {
            $countQuery->andWhere('`caches`.`type` = :cacheType');
            $countQuery->setParameter(':cacheType', $cacheType);
        }
        if ($bEvents) {
            $countQuery->andWhere('`date_hidden` >= curdate()');
        }

        $count = $countQuery->execute()->fetchColumn();

        $pager = new pager('newcaches.php?startat={offset}' . $cacheype_par);
    } else {
        $countQuery->andWhere('`caches`.`country`= :country');
        $countQuery->setParameter('country', $country);

        $count = $countQuery->execute()->fetchColumn();

        $pager = new pager('newcaches.php?country=' . $country . '&startat={offset}' . $cacheype_par);
    }
    $pager->make_from_offset($startAt, $count, 100);

    $tpl->assign('defaultcountry', $opt['template']['default']['country']);
    $tpl->assign('countryCode', $country);
    $tpl->assign(
        'countryName',
        $connection->fetchColumn(
            'SELECT IFNULL(`sys_trans_text` . `text`, `countries` . `name`)
             FROM `countries`
             LEFT JOIN `sys_trans`
               ON `countries` . `trans_id` = `sys_trans` . `id`
             LEFT JOIN `sys_trans_text`
               ON `sys_trans` . `id` = `sys_trans_text` . `trans_id`
               AND `sys_trans_text` . `lang` = :language
             WHERE `countries` . `short` = :countryCode',
            [
                ':language' => $opt['template']['locale'],
                ':countryCode' => $country ? $country : $login->getUserCountry(),
            ]
        )
    );
    $tpl->assign(
        'mainCountryName',
        $connection->fetchColumn(
            'SELECT IFNULL(`sys_trans_text` . `text`, `countries` . `name`)
             FROM `countries`
             LEFT JOIN `sys_trans`
               ON `countries` . `trans_id` = `sys_trans` . `id`
             LEFT JOIN `sys_trans_text`
               ON `sys_trans` . `id` = `sys_trans_text` . `trans_id`
               AND `sys_trans_text` . `lang` = :language
             WHERE `countries` . `short` = :country',
            [
                ':country' => $opt['page']['main_country'],
                ':language' => $opt['template']['locale'],
            ]
        )
    );

    $tpl->assign('events', $bEvents);
}

$tpl->display();
