<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCacheLogsEntity;
use Oc\Repository\Exception\RecordNotFoundException;

class SupportVandalismRepository
{
    private Connection $connection;

    private CachesRepository $cachesRepository;

    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @throws Exception
     */
    function getArchiveData(int $cacheId, string $wpID): array
    {
        $data = [];
        $admins = [];
        $userId = 0;

        // make waypoint index
        $wp_oc[$cacheId] = $wpID;

        // process cache coordinates
        $rs = $this->connection->createQueryBuilder()
                ->select('cache_id', 'LEFT(date_created, 10) AS date_modified', 'longitude', 'latitude', 'restored_by')
                ->from('cache_coordinates')
                ->where('cache_id = :paramID')
                ->setparameters(['paramID' => $cacheId])
                ->orderby('date_created', 'ASC')
                ->executeQuery()
                ->fetchAllAssociative();
        // order is relevant, because multiple changes per day possible
        $lastcoord = [];
        foreach ($rs as $r) {
            $coord = new CoordinatesRepository((float)$r['latitude'], (float)$r['longitude']);
            $coord = $coord->getDegreeMinutes();
            $coord = $coord['lat'] . " " . $coord['lon'];
            if (isset($lastcoord[$r['cache_id']]) && $coord != $lastcoord[$r['cache_id']]) {
                $this->appendData($data, $admins, $wp_oc, $r, "coord", $lastcoord[$r['cache_id']], $coord);
            }
            $lastcoord[$r['cache_id']] = $coord;
        }

        // process cache country
        $rs = $this->connection->createQueryBuilder()
                ->select('cache_id', 'LEFT(date_created, 10) AS date_modified', 'country', 'restored_by')
                ->from('cache_countries')
                ->where('cache_id = :paramID')
                ->setparameters(['paramID' => $cacheId])
                ->orderby('date_created', 'ASC')
                ->executeQuery()
                ->fetchAllAssociative();
        // order is relevant, because multiple changes per day possible
        $lastcountry = [];
        foreach ($rs as $r) {
            if (isset($lastcountry[$r['cache_id']]) && $r['country'] != $lastcountry[$r['cache_id']]) {
                $this->appendData($data, $admins, $wp_oc, $r, "country", $lastcountry[$r['cache_id']], $r['country']);
            }
            $lastcountry[$r['cache_id']] = $r['country'];
        }

        // process all other cache data
        // first the current data ...
        $nextcd = [];
        $rs = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('caches')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            $nextcd[$r['wp_oc']] = $r;
            $userId = $r['user_id']; // is used later for logs
        }

        // ... and then the changes
        $rs = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('caches_modified')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->orderBy('date_modified', 'DESC')
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            $wp = $wp_oc[$r['cache_id']];
            if ($r['name'] != $nextcd[$wp]['name']) {
                $this->appendData($data, $admins, $wp_oc, $r, 'name', $r['name'], $nextcd[$wp]['name']);
            }
            if ($r['type'] != $nextcd[$wp]['type']) {
                $this->appendData(
                        $data,
                        $admins,
                        $wp_oc,
                        $r,
                        'type',
                        $this->connection->createQueryBuilder()
                                ->select('name')
                                ->from('cache_type')
                                ->where('id = :paramID')
                                ->setParameters(['paramID' => $r['type']])
                                ->executeQuery()
                                ->fetchAssociative()['name'],
                        $this->connection->createQueryBuilder()
                                ->select('name')
                                ->from('cache_type')
                                ->where('id = :paramID')
                                ->setParameters(['paramID' => $nextcd[$wp]['type']])
                                ->executeQuery()
                                ->fetchAssociative()['name']
                );
                if ($r['size'] != $nextcd[$wp]['size']) {
                    $this->appendData(
                            $data,
                            $admins,
                            $wp_oc,
                            $r,
                            "size",
                            $this->connection->createQueryBuilder()
                                    ->select('name')
                                    ->from('cache_size')
                                    ->where('id = :paramID')
                                    ->setParameters(['paramID' => $r['size']])
                                    ->executeQuery()
                                    ->fetchAssociative()['name'],
                            $this->connection->createQueryBuilder()
                                    ->select('name')
                                    ->from('cache_size')
                                    ->where('id = :paramID')
                                    ->setParameters(['paramID' => $nextcd[$wp]['size']])
                                    ->executeQuery()
                                    ->fetchAssociative()['name']
                    );
                }
                if ($r['difficulty'] != $nextcd[$wp]['difficulty']) {
                    $this->appendData($data, $admins, $wp_oc, $r, "D", $r['difficulty'] / 2, $nextcd[$wp]['difficulty'] / 2);
                }
                if ($r['terrain'] != $nextcd[$wp]['terrain']) {
                    $this->appendData($data, $admins, $wp_oc, $r, "T", $r['terrain'] / 2, $nextcd[$wp]['terrain'] / 2);
                }
                if ($r['search_time'] != $nextcd[$wp]['search_time']) {
                    $this->appendData(
                            $data,
                            $admins,
                            $wp_oc,
                            $r,
                            'time',
                            $r['search_time'] . '&nbsp;h',
                            $nextcd[$wp]['search_time'] . '&nbsp;h'
                    );
                }
                if ($r['way_length'] != $nextcd[$wp]['way_length']) {
                    $this->appendData(
                            $data,
                            $admins,
                            $wp_oc,
                            $r,
                            'way',
                            $r['way_length'] . '&nbsp;km',
                            $nextcd[$wp]['way_length'] . '&nbsp;km'
                    );
                }
                if ($r['wp_gc'] != $nextcd[$wp]['wp_gc']) {
                    $this->appendData(
                            $data,
                            $admins,
                            $wp_oc,
                            $r,
                            'GC ',
                            $this->format_wp($r['wp_gc']),
                            $this->format_wp($nextcd[$wp]['wp_gc'])
                    );
                }
//                if ($r['wp_nc'] != $nextcd[$wp]['wp_nc']) {
//                    $this->appendData(
//                            $data,
//                            $admins,
//                            $wp_oc,
//                            $r,
//                            'GC ',
//                            $this->format_wp($r['wp_nc']),
//                            $this->format_wp($nextcd[$wp]['wp_nc'])
//                    );
//                }
                if ($r['date_hidden'] != $nextcd[$wp]['date_hidden']) {
                    $this->appendData($data, $admins, $wp_oc, $r, "hidden", $r['date_hidden'], $nextcd[$wp]['date_hidden']);
                }

                $nextcd[$wp] = $r;
            }
        }

        // attributes
        $rs = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('caches_attributes_modified')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->orderBy('date_modified', 'ASC')
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            $this->appendData(
                    $data,
                    $admins,
                    $wp_oc,
                    $r,
                    'attrib',
                    ($r['was_set'] ? '-' : '+') . $this->connection->createQueryBuilder()
                            ->select('name')
                            ->from('cache_attrib')
                            ->where('id = :paramID')
                            ->setParameters(['paramID' => $r['attrib_id']])
                            ->executeQuery()
                            ->fetchAssociative()['name'],
                    ''
            );
        }

        // descriptions
        // first the current data ...
        $nextdesc = [];
        $rs = $this->connection->createQueryBuilder()
                ->select('cache_id', 'language', 'LENGTH(`desc`) AS dl', 'LENGTH(hint) AS hl', 'LENGTH(short_desc) AS sdl')
                ->from('cache_desc')
                ->where('cache_id =:paramID')
                ->setParameters(['paramID' => $cacheId])
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            if (!isset($nextdesc[$r['cache_id']])) {
                $nextdesc[$r['cache_id']] = [];
            }
            $nextdesc[$r['cache_id']][$r['language']] = $r;
        }
        // ... and then the changes
        $rs = $this->connection->createQueryBuilder()
                ->select(
                        'cache_id',
                        'date_modified',
                        'language',
                        'LENGTH(`desc`) AS dl',
                        'LENGTH(hint) AS hl',
                        'LENGTH(short_desc) AS sdl',
                        'restored_by'
                )
                ->from('cache_desc_modified')
                ->where('cache_id =:paramID')
                ->setParameters(['paramID' => $cacheId])
                ->orderBy('date_modified', 'DESC')
                ->executeQuery()
                ->fetchAllAssociative(); // order doesn't matter as long only one change per day is recorded

        foreach ($rs as $r) {
            if (!isset($nextdesc[$r['cache_id']]) || !isset($nextdesc[$r['cache_id']][$r['language']])) {
                $next = [
                        'dl' => 0,
                        'hl' => 0,
                        'sdl' => 0
                ];
            } else {
                $next = $nextdesc[$r['cache_id']][$r['language']];
            }

            if ($r['dl'] + 0 != $next['dl'] + 0) {
                $this->appendData(
                        $data,
                        $admins,
                        $wp_oc,
                        $r,
                        'desc(' . $r['language'] . ')',
                        $r['dl'] + 0,
                        ($next['dl'] + 0) . ' bytes'
                );
            }
            if ($r['hl'] + 0 != $next['hl'] + 0) {
                $this->appendData(
                        $data,
                        $admins,
                        $wp_oc,
                        $r,
                        'hint(' . $r['language'] . ')',
                        $r['hl'] + 0,
                        ($next['hl'] + 0) . ' bytes'
                );
            }
            if ($r['sdl'] + 0 != $next['sdl'] + 0) {
                $this->appendData(
                        $data,
                        $admins,
                        $wp_oc,
                        $r,
                        'shortdesc(' . $r['language'] . ')',
                        $r['sdl'] + 0,
                        ($next['sdl'] + 0) . ' bytes'
                );
            }

            $nextdesc[$r['cache_id']][$r['language']] = $r;
        }

        // logs
        $rs = $this->connection->executeQuery(
                'SELECT `op`,
                LEFT(`date_modified`,10) AS `date_modified`,
                `cache_id`,
                `logs`.`user_id`,
                `type`,
                `date`,
                `restored_by`,
                `username`
         FROM (SELECT 1 AS `op`,
                      `deletion_date` AS `date_modified`,
                      `cache_id`,
                      `user_id`,
                      `type`,
                      `date`,
                      `restored_by`
               FROM `cache_logs_archived`
               WHERE `cache_id` IN (' . $cacheId . ')
                 AND `deleted_by`=:paramUserID AND `user_id`<> :paramUserID
                 UNION
                  SELECT 2 AS `op`, `date_modified`, `cache_id`,
                       (SELECT `user_id` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       (SELECT `type` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       (SELECT `date` FROM `cache_logs_archived` WHERE `id`=`original_id`),
                       `restored_by`
                 FROM `cache_logs_restored`
                  WHERE `cache_id` IN (' . $cacheId . ')
                  ) `logs`
                INNER JOIN `user` ON `user`.`user_id`=`logs`.`user_id`
              ORDER BY `logs`.`date_modified` ASC',
                ['paramID' => $cacheId, 'paramUserID' => $userId]
        )->fetchAllAssociative();

        foreach ($rs as $r) {
            $this->appendData(
                    $data,
                    $admins,
                    $wp_oc,
                    $r,
                    $r['op'] == 1 ? 'dellog' : 'restorelog',
                    "<a href='/user/profile/" . $r['user_id'] .
                    "' target='_blank'>" . $r['username'] . '</a>/' . $r['date'],
                    ''
            );
        }

        // pictures
        /* For sake of simplification, we
         *   - have stored the name of inserted pictures in pictures_modified
         *   - give no detailed information on picture property changes. This will be very
         *       rare in case of vandalism ...
         */
        $piccacheid = "IF(`object_type`=2, `object_id`, IF(`object_type`=1, IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)), 0))";
        $rs = $this->connection->executeQuery(
                'SELECT *, ' . $piccacheid . 'AS `cache_id` FROM `pictures_modified`
         WHERE ' . $piccacheid . ' IN (' . $cacheId . ') ' . '
         ORDER BY `date_modified` ASC'
        ) // order is relevant for the case of restore-reverts
        ->fetchAllAssociative();

        foreach ($rs as $r) {
            $picchange = '';

            $r['date_modified'] = substr($r['date_modified'], 0, 10);
            switch ($r['operation']) {
                case 'I':
                    $picchange = 'add';
                    break;
                case 'U':
                    $picchange = 'mod';
                    break;
                case 'D':
                    $picchange = 'del';
                    break;
            }
            switch ($r['object_type']) {
                case 1:
                    $picchange .= ' - log';
                    break;
                case 2:
                    $picchange .= ' - cache';
                    break;
            }
            $this->appendData($data, $admins, $wp_oc, $r, $picchange . 'pic', $r['title'], '');
        }

        // admins
        foreach ($admins as $adate => $adata) {
            foreach ($adata as $awp => $alist) {
                $data[$adate][$awp] .= "<br /><strong class='adminrestore'>admins:</strong> " . implode(',', $alist);
            }
        }

        // done
        ksort($data);

        return array_reverse($data, true);
    }

    /**
     * @throws RecordNotFoundException
     * @throws Exception
     */
    function restoreListings(array $roptions): array
    {
        // Set local SQL user variable. It is needed for some database triggers!
        // Set it to 0 at end of function
        $this->connection->executeStatement('SET @restoredby = CAST(' . $roptions['adminUserID'] . ' as UNSIGNED)');

        $wp = $roptions['wpID'];
        $currentAdminID = $roptions['adminUserID'];
        $cacheId = $this->cachesRepository->getIdByWP($wp);
        $rdate = $roptions['dateselect'];
        $simulate = key_exists('simulate', $roptions);

        $restored = [];
        $userId = (int)$this->connection->createQueryBuilder()
                ->select('user_id')
                ->from('caches')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->executeQuery()
                ->fetchAssociative()['user_id'];

        // coordinates
        if (key_exists('restore_coords_and_country', $roptions) &&
                !empty(
                $this->connection->createQueryBuilder()
                        ->select('cache_id')
                        ->from('cache_coordinates')
                        ->where('cache_id = :paramID')
                        ->setParameters(['paramID' => $cacheId])
                        ->executeQuery()
                        ->fetchAssociative()
                )
        ) {
            $rs = $this->connection->createQueryBuilder()
                    ->select('latitude', 'longitude')
                    ->from('cache_coordinates')
                    ->where('cache_id = :paramID')
                    ->andWhere('date_created < :paramDate')
                    ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                    ->orderBy('date_created', 'DESC')
                    ->executeQuery()
                    ->fetchAssociative();

            if (!empty($rs)) {
                if (!$simulate) {
                    $this->connection->createQueryBuilder()
                            ->update('caches')
                            ->set('latitude', ':paramLat')
                            ->set('longitude', ':paramLon')
                            ->where('cache_id = :paramID')
                            ->setParameters(
                                    ['paramLat' => $rs['latitude'], 'paramLon' => $rs['longitude'], 'paramID' => $cacheId]
                            )
                            ->executeStatement();
                }
                $restored[$wp]['coords'] = true;
            }
        }

        // country
        if (key_exists('restore_coords_and_country', $roptions) &&
                !empty(
                $this->connection->createQueryBuilder()
                        ->select('cache_id')
                        ->from('cache_countries')
                        ->where('cache_id = :paramID')
                        ->andWhere('date_created >= :paramDate')
                        ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                        ->executeQuery()
                        ->fetchAssociative()
                )
        ) {
            $rs = $this->connection->createQueryBuilder()
                    ->select('country')
                    ->from('cache_countries')
                    ->where('cache_id = :paramID')
                    ->andWhere('date_created < :paramDate')
                    ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                    ->orderBy('date_created', 'DESC')
                    ->executeQuery()
                    ->fetchAssociative();

            if (!empty($rs)) { // should always be true ...
                if (!$simulate) {
                    $this->connection->createQueryBuilder()
                            ->update('caches')
                            ->set('country', ':paramCountry')
                            ->where('cache_id = :paramID')
                            ->setParameters(['paramCountry' => $rs['country'], 'paramID' => $cacheId])
                            ->executeStatement();
                }

                $restored[$wp]['country'] = true;
            }
        }

        // other cache data
        $fields = [
                'name' => 'settings',
                'type' => 'settings',
                'size' => 'settings',
                'date_hidden' => 'settings',
                'difficulty' => 'settings',
                'terrain' => 'settings',
                'search_time' => 'settings',
                'way_length' => 'settings',
                'wp_gc' => 'waypoints',
//                'wp_nc' => 'waypoints'
        ];

        $rs = $this->connection->createQueryBuilder()
                ->select(' * ')
                ->from('caches_modified')
                ->where('cache_id = :paramID')
                ->andWhere('date_modified >= :paramDate')
                ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                ->orderBy('date_modified', 'ASC')
                ->executeQuery()
                ->fetchAssociative();

        if (!empty($rs)) {
            $rsx = $this->connection->createQueryBuilder()
                    ->update('caches')
                    ->where(('cache_id = :paramID'))
                    ->setParameters(['paramID' => $cacheId]);
            foreach ($fields as $field => $ropt) {
                $rsx->set($field, ':param' . $field)
                        ->setParameter('param' . $field, $rs[$field]);
                $restored[$wp][$field] = true;
            }
            if (!empty($rsx->getParameters()) && !$simulate) {
                $rsx->executeStatement();
            }
        }

        // attributes
        if (key_exists('restore_attributes', $roptions)) {
            $rs = $this->connection->createQueryBuilder()
                    ->select(' * ')
                    ->from('caches_attributes_modified')
                    ->where('cache_id = :paramID')
                    ->andWhere('date_modified >= :paramDate')
                    ->andWhere('attrib_id != 6')
                    ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                    ->orderBy('date_modified', 'DESC')
                    ->executeQuery()
                    ->fetchAllAssociative();

            // revert all attribute changes in reverse order.
            // recording limit of one change per attribute, cache and day ensures that no exponentially
            // growing list of recording entries can emerge from multiple reverts.
            foreach ($rs as $r) {
                if (!$simulate) {
                    if ($r['was_set']) {
                        // INSERT nur, wenn DB noch keinen identischen Eintrag enthält
                        if (!$this->connection->createQueryBuilder()
                                ->select(' * ')
                                ->from('caches_attributes')
                                ->where('cache_id = :paramID')
                                ->andWhere('attrib_id = :paramAttribID')
                                ->setParameters(['paramID' => $cacheId, 'paramAttribID' => $r['attrib_id']])
                                ->executeQuery()
                                ->fetchAssociative()
                        ) {
                            $this->connection->createQueryBuilder()
                                    ->insert('caches_attributes')
                                    ->values(['cache_id' => ':paramID', 'attrib_id' => ':paramAttribID'])
                                    ->setParameters(['paramID' => $cacheId, 'paramAttribID' => $r['attrib_id']])
                                    ->executeStatement();
                        }
                    } else {
                        $this->connection->createQueryBuilder()
                                ->delete('caches_attributes')
                                ->where('cache_id = :paramID')
                                ->andWhere('attrib_id = :paramAttribID')
                                ->setParameters(['paramID' => $cacheId, 'paramAttribID' => $r['attrib_id']])
                                ->executeStatement();
                    }
                }
                $restored[$wp]['attributes'] = true;
            }
        }

        // descriptions
        if (key_exists('restore_desc_pictures', $roptions)) {
            $rs = $this->connection->createQueryBuilder()
                    ->select(' * ')
                    ->from('cache_desc_modified')
                    ->where('cache_id = :paramID')
                    ->andWhere('date_modified >= :paramDate')
                    ->setParameters(['paramID' => $cacheId, 'paramDate' => $rdate])
                    ->orderBy('date_modified', 'DESC')
                    ->executeQuery()
                    ->fetchAllAssociative();

            // revert all desc changes in reverse order.
            // recording limit of one change per language, cache and day ensures that no exponentially
            // growing list of recording entries can emerge from restore-reverts.
            foreach ($rs as $r) {
                if (!$simulate) {
                    if ($r['desc'] === null) { // was newly created -> delete
                        $this->connection->createQueryBuilder()
                                ->delete('cache_desc')
                                ->where('cache_id = :paramID')
                                ->andWhere('language = :paramLanguage')
                                ->setParameters(['paramID' => $cacheId, 'paramLanguage' => $r['language']])
                                ->executeStatement();
                    } else {
                        $node = (int)$this->connection->createQueryBuilder()
                                ->select('node')
                                ->from('caches')
                                ->where('cache_id = :paramID')
                                ->setParameters(['paramID' => $cacheId])
                                ->executeQuery()
                                ->fetchAssociative()['node'];

                        $rd = $this->connection->createQueryBuilder()
                                ->select(' * ')
                                ->from('cache_desc')
                                ->where('cache_id = :paramID')
                                ->setParameters(['paramID' => $cacheId])
                                ->executeQuery()
                                ->fetchAllAssociative();

                        if (empty($rd)) {
                            // desc entry not existent in DB ... insert
                            // id, uuid, date_created and last_modified are set automatically
                            $this->connection->createQueryBuilder()
                                    ->insert('cache_desc')
                                    ->values([
                                            'node' => ':paramNode',
                                            'cache_id' => ':paramID',
                                            'language' => ':paramLanguage',
                                            'desc' => ':paramDesc',
                                            'desc_html' => ':paramDescHtml',
                                            'desc_htmledit' => ':paramDescHtmlEdit',
                                            'hint' => ':paramHint',
                                            'short_desc' => ':paramShortDesc'
                                    ])
                                    ->setParameters([
                                            'paramNode' => $node,
                                            'paramID' => $cacheId,
                                            'paramLanguage' => $r['language'],
                                            'paramDesc' => $r['desc'],
                                            'paramDescHtml' => $r['desc_html'],
                                            'paramDescHtmlEdit' => $r['desc_htmledit'],
                                            'paramHint' => $r['hint'],
                                            'paramShortDesc' => $r['short_desc']
                                    ])
                                    ->executeStatement();
                        } else {
                            // desc entry existent in DB  ... update
                            $this->connection->createQueryBuilder()
                                    ->update('cache_desc')
                                    ->set('node', ':paramNode')
                                    ->set('language', ':paramLanguage')
                                    ->set('`desc`', ':paramDesc')
                                    ->set('desc_html', ':paramDescHtml')
                                    ->set('desc_htmledit', ':paramDescHtmlEdit')
                                    ->set('hint', ':paramHint')
                                    ->set('short_desc', ':paramShortDesc')
                                    ->where('node = :paramNode')
                                    ->andwhere('cache_id = :paramID')
                                    ->andWhere('language = :paramLanguage')
                                    ->setParameters([
                                            'paramNode' => $node,
                                            'paramID' => $cacheId,
                                            'paramLanguage' => $r['language'],
                                            'paramDesc' => $r['desc'],
                                            'paramDescHtml' => $r['desc_html'],
                                            'paramDescHtmlEdit' => $r['desc_htmledit'],
                                            'paramHint' => $r['hint'],
                                            'paramShortDesc' => $r['short_desc']
                                    ])
                                    ->executeQuery();
                        }
                    }
                }

                $restored[$wp]['description(s)'] = true;
            }
        }

        // logs
        if (key_exists('restore_logs_pictures', $roptions)) {
            $rs = $this->connection->executeQuery(
                    'SELECT * FROM(
            SELECT
                        `id`,
                        -1 as `node`,
                        `date_modified`,
                        `cache_id`,
                        0 as `user_id`,
                        0 as `type`,
                        null as `oc_team_comment`,
                        null as `date`,
                        null as `text`,
                        0 as `text_html`,
                        0 as `text_htmledit`,
                        0 as `needs_maintenance`,
                        0 as `listing_outdated`,
                        `original_id`
                    FROM `cache_logs_restored`
                    WHERE `cache_id` =:paramID AND `date_modified` >= :paramModDate
                    UNION
                    SELECT
                        `id`,
                        `node`,
                        `deletion_date`,
                        `cache_id`,
                        `user_id`,
                        `type`,
                        `oc_team_comment`,
                        `date`,
                        `text`,
                        `text_html`,
                        `text_htmledit`,
                        `needs_maintenance`,
                        `listing_outdated`,
                        0 as `original_id`
                    FROM `cache_logs_archived`
                    WHERE
                        `cache_id` =:paramID
                        AND `deletion_date` >= :paramModDate
                        AND `deleted_by` = :paramUserID
                        AND `user_id` != :paramUserID
                ) `logs`
                ORDER BY `date_modified` ASC',
                    ['paramID' => $cacheId, 'paramModDate' => $rdate, 'paramUserID' => $userId]
            )->fetchAllAssociative();

            // We start with the oldest entry and will touch each log only once:
            // After restoring its state, it is added to $logs_processed (by its last known id),
            // and all further operations on the same log are ignored. This prevents unnecessary
            // operations and flooding pictures_modified on restore-reverts.
            $logs_processed = [];

            foreach ($rs as $r) {
                $logs_restored = false;

                // the log's id may have changed by multiple delete -and-restores
                $revert_logid = $this->get_current_logid((int)$r['id']);

                if (key_exists($revert_logid, $logs_processed)) {
                    if ($r['node'] == -1) {
                        // if it was not already deleted by a later restore operation ...
                        if (!$this->connection->createQueryBuilder()
                                ->select('id')
                                ->from('cache_logs')
                                ->where('id = :paramID')
                                ->setParameters(['paramID' => $revert_logid])
                                ->executeQuery()
                                ->fetchAssociative()
                        ) {
                            if (!$simulate) {
                                $this->connection->executeStatement(
                                        'INSERT INTO `cache_logs_archived`
                                     SELECT *, `0`, `:paramOriginalUserID`, `:paramLoginUID` FROM `cache_logs` WHERE `id`=:paramRevertLogID',
                                        [
                                                'paramOriginalUserID' => $userId,
                                                'paramLoginUID' => $currentAdminID, // original deletor's ID and not restoring admin's ID!
                                                'paramRevertLogID' => $revert_logid
                                        ]
                                );

                                $this->connection->createQueryBuilder()
                                        ->delete('cache_logs')
                                        ->where('id = :paramID')
                                        ->setParameters(['paramID' => $revert_logid])
                                        ->executeStatement();

                                // This triggers an okapi_syncbase update, if OKAPI is installed:
                                $this->connection->createQueryBuilder()
                                        ->update('cache_logs_archived')
                                        ->set('deletion_date', ':paramNOW')
                                        ->where('id = :paramID')
                                        ->setParameters(
                                                ['paramID' => $revert_logid, 'paramNOW' => (new DateTime("now"))->format('Y-m-d H:i:s')]
                                        )
                                        ->executeStatement();
                            }
                            $logs_restored = true;
                        }
                        // if it was not already restored by a later restore operation ...
                    } elseif (!$this->connection->createQueryBuilder()
                            ->select('id')
                            ->from('cache_logs')
                            ->where('id = :paramID')
                            ->setParameters(['paramID' => $revert_logid])
                            ->executeQuery()
                            ->fetchAssociative()
                    ) {
                        // id, uuid, date_created and last_modified are set automatically;
                        // picture will be updated automatically on picture-restore
                        // assign node ... cachelog class currently does not initialize node field
                        $log = new GeoCacheLogsEntity([
                                'id' => (int)$this->connection->lastInsertId('cache_logs_restored') + 1,
                                'node' => $r['node'],
                                'cacheId' => $r['cache_id'],
                                'userId' => $r['user_id'],
                                'type' => $r['type'],
                                'ocTeamComment' => $r['oc_team_comment'],
                                'date' => $r['date'],
                                'text' => $r['text'],
                                'textHtml' => $r['text_html'],
                                'textHtmledit' => $r['text_htmledit'],
                                'needsMaintenance' => $r['needs_maintenance'],
                                'listingOutdated' => $r['listing_outdated'],
                                'ownerNotified' => 1
                        ]);
                        if (!$simulate) {
                            $this->connection->executeStatement(
                                    'INSERT IGNORE INTO `cache_logs_restored`
                                      (`id`, `date_modified`, `cache_id`, `original_id`, `restored_by`)
                                    VALUES (:paramID, :paramNOW, :paramCacheID, :paramOriginalID, :paramRestoredByID)',
                                    [
                                            'paramID' => $log->id,
                                            'paramNOW' => (new DateTime("now"))->format('Y-m-d H:i:s'),
                                            'paramCacheID' => $r['cache_id'],
                                            'paramOriginalID' => $revert_logid,
                                            'paramRestoredByID' => $currentAdminID
                                    ]
                            );

                            // watches_logqueue entry was created by trigger
                            $this->connection->createQueryBuilder()
                                    ->delete('watches_logqueue')
                                    ->where('log_id = :paramLogID')
                                    ->setParameters(['paramLogID' => $log->id])
                                    ->executeStatement();

                            $logs_processed[] = $log->id;
                        }
                        $logs_restored = true;
                    }  // restore deleted

                    $logs_processed[] = $revert_logid;
                }

                if ($logs_restored) {
                    $restored[$wp]['logs'] = true;
                }
            }
        }

        // pictures
        if (key_exists('restore_desc_pictures', $roptions) || key_exists('restore_logs_pictures', $roptions)) {
            // TODO: Adaption von htdocs/restorecaches.php fehlt noch
//            $rs = sql(
//                    "SELECT *FROM `pictures_modified`
//                        WHERE ((`object_type`=2 AND '&2' AND `object_id`='&3') OR
//                                           (`object_type`=1 AND '&1'
//                                                  AND IFNULL((SELECT `user_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `user_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)) != '&5'
//                                                  /* ^^ ignore changes of own log pics (shouldnt be in pictures_modified, anyway) */
//                                                  AND IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)) = '&3'))
//                          AND `date_modified`>='&4'
//                                    ORDER BY `date_modified` ASC",
//                    in_array("logs", $roptions) ? 1 : 0,
//                    in_array("desc", $roptions) ? 1 : 0,
//                    $cacheId,
//                    $rdate,
//                    $userId
//            );

            // We start with the oldest entry and will touch each picture ony once:
            // After restoring its state, it is added to $pics_processed (by its last known id),
            // and all further operations on the same pic are ignored. This prevents unnecessary
            // operations and flooding the _modified table on restore-reverts.
            $pics_processed = [];

//            while ($r = sql_fetch_assoc($rs)) {
//                $pics_restored = false;
//
//                // the picture id may have changed by multiple delete-and-restores
//                $revert_picid = get_current_picid($r['id']);
//                if (!in_array($revert_picid, $pics_processed)) {
//                    // .. as may have its uuid-based url
//                    $revert_url = sql_value(
//                            "SELECT `url` FROM `pictures_modified` WHERE `id`='&1'",
//                            $r['url'],
//                            $revert_picid
//                    );
//                    $error = "";
//
//                    switch ($r['operation']) {
//                        case 'I':
//                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) != 0) {
//                                // if it was not already deleted by a later restore operation:
//                                // delete added (cache) picture
//                                $pic = new picture($revert_picid);
//                                if ($simulate) {
//                                    $pics_restored = true;
//                                } else {
//                                    if ($pic->delete(true)) {
//                                        $pics_restored = true;
//                                    } else {
//                                        $error = "delete";
//                                    }
//                                }
//                            }
//                            break;
//
//                        case 'U':
//                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) != 0) {
//                                // if it was not deleted by a later restore operation:
//                                // restore modified (cache) picture properties
//                                $pic = new picture($revert_picid);
//                                $pic->setTitle($r['title']);
//                                $pic->setSpoiler($r['spoiler']);
//                                $pic->setDisplay($r['display']);
//                                // mappreview flag is not restored, because it seems unappropriate to
//                                // advertise for the listing of a vandalizing owner
//
//                                if ($simulate) {
//                                    $pics_restored = true;
//                                } else {
//                                    if ($pic->save(true)) {
//                                        $pics_restored = true;
//                                    } else {
//                                        $error = "update";
//                                    }
//                                }
//                            }
//                            break;
//
//                        case 'D':
//                            if (sql_value("SELECT `id` FROM `pictures` WHERE `id`='&1'", 0, $revert_picid) == 0) {
//                                // if it was not already restored by a later restore operation:
//                                // restore deleted picture
//                                // id, uuid, date_created and last_modified are set automatically
//
//                                // the referring log's id  may have changed by [multiple] delete-and-restore
//                                if ($r['object_type'] == 1) {
//                                    $r['object_id'] = get_current_logid($r['object_id']);
//                                }
//
//                                // id, uuid, node, date_created, date_modified are automatically set;
//                                // url will be set on save;
//                                // last_url_check and thumb_last_generated stay at defaults until checked;
//                                // thumb_url will be set on thumb creation (old thumb was deleted)
//                                $pic = new picture();
//                                $pic->setTitle($r['title']);
//                                $pic->setObjectId($r['object_id']);
//                                $pic->setObjectType($r['object_type']);
//                                $pic->setSpoiler($r['spoiler']);
//                                $pic->setLocal(1);
//                                $pic->setUnknownFormat($r['unknown_format']);
//                                $pic->setDisplay($r['display']);
//                                // mappreview flag is not restored, because it seems unappropriate to
//                                // advertise for the listing of a vandalizing owner
//
//                                if ($simulate) {
//                                    $pics_restored = true;
//                                } else {
//                                    if ($pic->save(true, $revert_picid, $revert_url)) {
//                                        $pics_restored = true;
//                                        $pics_processed[] = $pic->getPictureId();
//                                    } else {
//                                        $error = "restore";
//                                    }
//                                }
//                            }
//                            break;
//                    }  // switch
//
//                    $pics_processed[] = $revert_picid;
//                }  // not already processed
//
//                if ($error != '') {
//                    $restored[$wp]['internal error - could not $error picture ' . $r['id'] . '/' . $picid] = true;
//                }
//                if ($pics_restored) {
//                    $restored[$wp]['pictures'] = true;
//                }
//            }  // foreach (all relevant pic records)
        }


        $this->connection->executeStatement('SET @restoredby = CAST(0 AS UNSIGNED)');

        return [$restored, $simulate];
    }

    /**
     * @throws Exception
     */
    function appendData(&$data, &$admins, $wp_oc, $r, $field, $oldvalue, $newvalue)
    {
        if (!isset($r['date_modified'])) {
            die('internal error: date_modified not set for: ' . $field);
        }
        $mdate = $r['date_modified'];
        $wp = $wp_oc[$r['cache_id']];
        $byadmin = ($r['restored_by'] > 0);

        if (!isset($data[$mdate])) {
            $data[$mdate] = [];
        }

        $text = '<strong';
        if ($byadmin) {
            $text .= " class='adminrestore'";
        } else {
            $text .= " class='userchange'";
        }
        $text .= ">$field</strong>: $oldvalue" . ($newvalue != '' ? " &rarr; $newvalue" : '');
        if (isset($data[$mdate][$wp])) {
            $data[$mdate][$wp] .= ', ' . $text;
        } else {
            $data[$mdate][$wp] = $text;
        }

        if ($byadmin) {
            if (!isset($admins[$mdate])) {
                $admins[$mdate] = [];
            }
            if (!isset($admins[$mdate][$wp])) {
                $admins[$mdate][$wp] = [];
            }

            $admins[$mdate][$wp][$r['restored_by'] + 0]
                    = "<a href='/user/profile/" . $r['restored_by'] . "' target='_blank'>" .
                    $this->connection->createQueryBuilder()
                            ->select('username')
                            ->from('user')
                            ->where('user_id = :paramUser')
                            ->setParameters(['paramUser' => $r['restored_by']])
                            ->executeQuery()
                            ->fetchAssociative()['username']
                    . '</a>';
        }
    }

    function format_wp($wp): string
    {
        if ($wp == '') {
            return '(leer)';
        } else {
            return $wp;
        }
    }

    /**
     * @throws Exception
     *
     * determine new id of a log if it has been deleted and restored [multiple times]
     */
    function get_current_logid(int $logid): int
    {
        do {
            $new_logid = $this->connection->createQueryBuilder()
                    ->select('id')
                    ->from('cache_logs_restored')
                    ->where('original_id = :paramID')
                    ->setParameters(['paramID' => $logid])
                    ->executeQuery()
                    ->fetchAssociative();

            if (!$new_logid) {
                $new_logid = 0;
            }

            if ($new_logid != 0) {
                $logid = $new_logid;
            }
        } while ($new_logid != 0);

        return $logid;
    }
}
