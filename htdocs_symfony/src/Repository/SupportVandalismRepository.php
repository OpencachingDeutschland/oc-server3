<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
            $user_id = $r['user_id']; // is used later for logs
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
        // TODO: verschachtelte SQL-Anweisung umdröseln in QueryBuilder-Abfrage.. sonst können vandalisierte Logs nicht ausgewertet werden
        // Die foreach-Schleife wurde bereits umgewandelt. Siehe /htdocs/restorecaches.php
//        $rs = sql(
//                'SELECT `op`,
//                LEFT(`date_modified`,10) AS `date_modified`,
//                `cache_id`,
//                `logs`.`user_id`,
//                `type`,
//                `date`,
//                `restored_by`,
//                `username`
//         FROM (SELECT 1 AS `op`,
//                      `deletion_date` AS `date_modified`,
//                      `cache_id`,
//                      `user_id`,
//                      `type`,
//                      `date`,
//                      `restored_by`
//               FROM `cache_logs_archived`
//               WHERE `cache_id` IN ' . $cachelist . "
//                 AND `deleted_by`='&1' AND `user_id`<>'&1'
//                 UNION
//                  SELECT 2 AS `op`, `date_modified`, `cache_id`,
//                       (SELECT `user_id` FROM `cache_logs_archived` WHERE `id`=`original_id`),
//                       (SELECT `type` FROM `cache_logs_archived` WHERE `id`=`original_id`),
//                       (SELECT `date` FROM `cache_logs_archived` WHERE `id`=`original_id`),
//                       `restored_by`
//                 FROM `cache_logs_restored`
//                  WHERE `cache_id` IN " . $cachelist . '
//                  ) `logs`
//                INNER JOIN `user` ON `user`.`user_id`=`logs`.`user_id`
//              ORDER BY `logs`.`date_modified` ASC',
//                // order may not be exact when redoing reverts, because delete and insert
//                // operations then are so quick that dates in both tables are the same
//                $user_id
//        );
//        foreach ($rs as $r) {
//            $this->appendData(
//                    $data,
//                    $admins,
//                    $wp_oc,
//                    $r,
//                    $r['op'] == 1 ? 'dellog' : 'restorelog',
//                    "<a href='viewprofile.php?userid=" . $r['user_id'] .
//                    "' target='_blank'>" . $r['username'] . '</a>/' . $r['date'],
//                    ''
//            );
//        }

        // pictures
        /* For sake of simplification, we
         *   - have stored the name of inserted pictures in pictures_modified
         *   - give no detailed information on picture property changes. This will be very
         *       rare in case of vandalism ...
         */
        // TODO: verschachtelte SQL-Anweisung umdröseln in QueryBuilder-Abfrage.. sonst können vandalisierte Bilder nicht ausgewertet werden
//        $piccacheid = "IF(`object_type`=2, `object_id`, IF(`object_type`=1, IFNULL((SELECT `cache_id` FROM `cache_logs` WHERE `id`=`object_id`),(SELECT `cache_id` FROM `cache_logs_archived` WHERE `id`=`object_id`)), 0))";
//        $rs = sql(
//                'SELECT *, ' . $piccacheid . 'AS `cache_id` FROM `pictures_modified`
//         WHERE ' . $piccacheid . ' IN ' . $cachelist . '
//         ORDER BY `date_modified` ASC'
//        ); // order is relevant for the case of restore-reverts
//        while ($r = sql_fetch_assoc($rs)) {
//            $r['date_modified'] = substr($r['date_modified'], 0, 10);
//            switch ($r['operation']) {
//                case 'I':
//                    $picchange = 'add';
//                    break;
//                case 'U':
//                    $picchange = 'mod';
//                    break;
//                case 'D':
//                    $picchange = 'del';
//                    break;
//            }
//            switch ($r['object_type']) {
//                case 1:
//                    $picchange .= '-log';
//                    break;
//                case 2:
//                    $picchange .= '-cache';
//                    break;
//            }
//            appendData($data, $admins, $wp_oc, $r, $picchange . 'pic', $r['title'], '');
//        }

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
        $this->connection->executeStatement('SET @restoredby = CAST(' . $roptions['adminUserID'] . ' AS UNSIGNED)');

        $wp = $roptions['wpID'];
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
        if (key_exists('restore_coords', $roptions) &&
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
        if (key_exists('restore_coords', $roptions) &&
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
                ->select('*')
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
        if (key_exists('restore_settings', $roptions)) {
            $rs = $this->connection->createQueryBuilder()
                    ->select('*')
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
                if ($simulate) {
                    if ($r['was_set']) {
                        // INSERT nur, wenn DB noch keinen identischen Eintrag enthält
                        if (!$this->connection->createQueryBuilder()
                                ->select('*')
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
        if (key_exists('restore_desc', $roptions)) {
            $rs = $this->connection->createQueryBuilder()
                    ->select('*')
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
                                // TODO: "ON DUPLICATE KEY UPDATE" fehlt im INSERT Statement!
                                // TODO: korrekte node-Info fehlt! 4 ist der Testserver
                                ->setParameters([
                                        'paramNode' => 4,
                                        'paramID' => $cacheId,
                                        'paramLanguage' => $r['language'],
                                        'paramDesc' => $r['desc'],
                                        'paramDescHtml' => $r['desc_html'],
                                        'paramDescHtmlEdit' => $r['desc_htmledit'],
                                        'paramHint' => $r['hint'],
                                        'paramShortDesc' => $r['short_desc']
                                ])
                                ->executeStatement();
                    }
                }

                $restored[$wp]['description(s)'] = true;
            }
        }

        // logs
        // TODO: Adaption von htdocs/restorecaches.php fehlt noch

        // pictures
        // TODO: Adaption von htdocs/restorecaches.php fehlt noch

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

        // TODO: HTML+CSS anpassen, da das vom Legacycode herauskopiert wurde und nun nicht mehr so recht passt
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
                    = $this->connection->createQueryBuilder()
                    ->select('username')
                    ->from('user')
                    ->where('user_id = :paramUser')
                    ->setParameters(['paramUser' => $r['restored_by']])
                    ->executeQuery()
                    ->fetchAssociative()['username'];
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
}
