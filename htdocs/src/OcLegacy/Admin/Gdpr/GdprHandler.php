<?php

namespace OcLegacy\Admin\Gdpr;

use Doctrine\DBAL\Connection;
use Exception;

class GdprHandler
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $projectDir;

    public function __construct(Connection $connection, string $projectDir)
    {
        $this->connection = $connection;
        $this->projectDir = $projectDir;
    }

    public function handle(\user $user, bool $execute = false): array
    {
        $userId = $user->getUserId();

        $caches = $this->getCaches($userId);
        $cacheLogs = $this->getCacheLogs($userId);

        $cachePictures = $this->fetchPictures($caches, 'cache_id', OBJECT_CACHE);
        $cacheLogPictures = $this->fetchPictures($cacheLogs, 'id', OBJECT_CACHELOG);

        $cachePicturesModified = $this->fetchPicturesModified($caches, 'cache_id', OBJECT_CACHE);
        $cacheLogPicturesModified = $this->fetchPicturesModified($cacheLogs, 'id', OBJECT_CACHELOG);

        $cacheCount = count($caches);
        $cacheLogCount = count($cacheLogs);
        $cachePicturesCount = count($cachePictures) + count($cachePicturesModified);
        $cacheLogPicturesCount = count($cacheLogPictures) + count($cacheLogPicturesModified);

        $executed = false;

        if ($execute) {
            try {
                $this->connection->beginTransaction();

                $this->processUser($userId);
                $this->processCaches($userId, $caches);
                $this->deletePictures($cachePictures);
                $this->deletePictures($cacheLogPictures);

                $this->deletePicturesModified($cachePicturesModified);
                $this->deletePicturesModified($cacheLogPicturesModified);

                $this->deleteCacheLogSubTables($userId);
                $this->deleteCacheIgnore($userId);
                $this->deleteFieldNotes($userId);
                $this->deleteLogEntries($userId);
                $this->deleteLogins($userId);
                $this->deleteOkapiAuthorizations($userId);
                $this->deleteWatches($userId);
                $this->deleteWsSessions($userId);

                $this->connection->commit();

                $executed = true;
            } catch (Exception $e) {
                $this->connection->rollBack();

                return [
                    'userId' => $userId,
                    'username' => $user->getUsername(),
                    'email' => $user->getEMail(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'userId' => $userId,
            'username' => $user->getUsername(),
            'email' => $user->getEMail(),
            'cacheCount' => $cacheCount,
            'cacheLogCount' => $cacheLogCount,
            'cachePicturesCount' => $cachePicturesCount,
            'cacheLogPicturesCount' => $cacheLogPicturesCount,
            'executed' => $executed,
        ];
    }

    private function processUser(int $userId): void
    {
        $this->anonymizeUser($userId);
        $this->deleteUserOptions($userId);
        $this->deleteUserCacheLists($userId);
        $this->deleteEmailUser($userId);
        $this->deleteQueries($userId);
    }

    private function processCaches(int $userId, array $caches): void
    {
        $this->anonymizeCaches($caches);
        $this->anonymizeCacheLogs($userId);
        $this->deleteCacheAdoptions($userId);
    }

    private function anonymizeUser(int $userId): void
    {
        $newUsername = 'delete_' . $userId;

        $this->connection->executeQuery(<<<SQL
            UPDATE user SET
                username = :username,
                last_login = NULL,
                password = NULL,
                roles = '',
                email = NULL,
                is_active_flag = 0,
                latitude = 0,
                longitude = 0,
                accept_mailing = 0,
                usermail_send_addr = 0,
                last_name = '',
                first_name = '',
                watchmail_mode = 0,
                watchmail_hour = 0,
                watchmail_nextmail = 0,
                watchmail_day = 0,
                statpic_logo = 0,
                statpic_text = '',
                notify_radius = 0,
                notify_oconly = 0,
                gdpr_deletion = 1,
                description = '',
                date_created = '1970-01-01 00:00:00',
                last_modified = '1970-01-01 00:00:00',
                last_email_problem = NULL,
                new_pw_code = NULL,
                new_pw_date = NULL,
                permanent_login_flag = 0,
                admin = 0,
                domain = NULL
            WHERE user_id = :userId
SQL
        , [
            'userId' => $userId,
            'username' => $newUsername,
        ]);
    }

    private function deleteCacheAdoptions(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM cache_adoptions WHERE from_user_id = :userId OR to_user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteUserOptions(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM user_options WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteUserCacheLists(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM cache_lists WHERE user_id = :userId', [
            'userId' => $userId,
        ]);

        $this->connection->executeQuery('DELETE FROM cache_list_watches WHERE user_id = :userId', [
            'userId' => $userId,
        ]);

        $this->connection->executeQuery('DELETE FROM cache_list_bookmarks WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function anonymizeCacheLogs(int $userId): void
    {
        $this->connection->executeQuery('UPDATE cache_logs SET text=\'-User gelöscht-\', gdpr_deletion = 1 WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteCacheLogSubTables(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM cache_logs_archived WHERE user_id = :userId', [
            'userId' => $userId,
        ]);

        $this->connection->executeQuery('DELETE FROM cache_logs_modified WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function getCaches(int $userId): array
    {
        return $this->connection->fetchAll('SELECT * FROM caches WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function getCacheLogs(int $userId): array
    {
        return $this->connection->fetchAll('SELECT * FROM cache_logs WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function anonymizeCaches(array $caches): void
    {
        foreach ($caches as $cache) {
            $this->connection->executeQuery(<<<'SQL'
                UPDATE caches SET
                    name='-Cache nach DSGVO gelöscht-',
                    show_cachelists = 0,
                    status = 6,
                    wp_gc = '',
                    wp_gc_maintained ='',
                    gdpr_deletion = 1
                WHERE cache_id = :cacheId;
SQL
            , [
                'cacheId' => (int) $cache['cache_id'],
            ]);

            $this->connection->executeQuery(<<<'SQL'
                UPDATE cache_desc SET
                    `desc` = '### DSGVO Löschung ###<br>Dieses Listing wurde aufgrund der Anforderungen des Urhebers, seine Daten im Rahmen des Datenschutzes zu löschen - neutralisiert.<br>Leider können wir keine Details aus dem Listing beibehalten. Die nicht personenbezogenen Parameter des Caches wie Lage, Wertung und die Logs Dritter können zum Erhalt des Spieles aber beibehalten.<br><br>###<br>Eurer OC Team - im Auftrag des Datenschutzbeauftragen',
                    desc_html = 1,
                    hint = '',
                    short_desc = ''
                WHERE cache_id = :cacheId
SQL
            , [
                'cacheId' => (int) $cache['cache_id'],
            ]);

            $this->connection->executeQuery('DELETE FROM cache_desc_modified WHERE cache_id = :cacheId', [
                'cacheId' => (int) $cache['cache_id'],
            ]);

            $this->connection->executeQuery('DELETE FROM cache_ignore WHERE cache_id = :cacheId', [
                'cacheId' => (int) $cache['cache_id'],
            ]);

            $this->connection->executeQuery('DELETE FROM caches_modified WHERE cache_id = :cacheId', [
                'cacheId' => (int) $cache['cache_id'],
            ]);
        }
    }

    public function fetchPictures(array $data, string $idField, int $objectType): array
    {
        if ($data === []) {
            return [];
        }

        $ids = array_map(static function (array $cache) use ($idField) {
            return (int) $cache[$idField];
        }, $data);

        $pictures = $this->connection->fetchAll('SELECT * FROM pictures WHERE object_id IN (' . implode(',', $ids) . ') AND object_type = :objectType', [
            'objectType' => $objectType,
        ]);

        $modifiedPictures = $this->connection->fetchAll('SELECT * FROM pictures_modified WHERE object_id IN (' . implode(',', $ids) . ') AND object_type = :objectType', [
            'objectType' => $objectType,
        ]);

        return array_merge($pictures, $modifiedPictures);
    }

    public function fetchPicturesModified(array $data, string $idField, int $objectType): array
    {
        if ($data === []) {
            return [];
        }

        $ids = array_map(static function (array $cache) use ($idField) {
            return (int) $cache[$idField];
        }, $data);

        return $this->connection->fetchAll('SELECT * FROM pictures_modified WHERE object_id IN (' . implode(',', $ids) . ') AND object_type = :objectType', [
            'objectType' => $objectType,
        ]);
    }

    private function deletePictures(array $pictures): void
    {
        foreach ($pictures as $picture) {
            $imagePath = parse_url($picture['url'], PHP_URL_PATH);
            @unlink($this->projectDir . $imagePath);

            if (isset($picture['thumb_url'])) {
                $thumbPath = parse_url($picture['thumb_url'], PHP_URL_PATH);
                @unlink($this->projectDir . $thumbPath);
            }

            $this->connection->executeQuery('DELETE FROM pictures WHERE object_id = :objectId AND object_type = :objectType', [
                'objectId' => $picture['object_id'],
                'objectType' => $picture['object_type'],
            ]);
        }
    }

    private function deletePicturesModified(array $pictures): void
    {
        foreach ($pictures as $picture) {
            $imagePath = parse_url($picture['url'], PHP_URL_PATH);

            @unlink($this->projectDir . $imagePath);

            $this->connection->executeQuery('DELETE FROM pictures_modified WHERE object_id = :objectId AND object_type = :objectType', [
                'objectId' => $picture['object_id'],
                'objectType' => $picture['object_type'],
            ]);
        }
    }

    private function deleteCacheIgnore(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM cache_ignore WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteEmailUser(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM email_user WHERE from_user_id = :userId OR to_user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteFieldNotes(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM field_note WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteLogEntries(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM logentries WHERE userid = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteLogins(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM logins WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteOkapiAuthorizations(int $userId): void
    {
        $this->connection->executeQuery(<<<'SQL'
            DELETE oa, oc
            FROM okapi_authorizations oa
            INNER JOIN okapi_consumers oc ON oa.consumer_key = oc.`key`
            INNER JOIN okapi_nonces ono ON oa.consumer_key = ono.consumer_key
            INNER JOIN okapi_diagnostics od ON oa.consumer_key = od.consumer_key
            INNER JOIN okapi_cache oca ON oa.consumer_key = oca.`key`
            INNER JOIN okapi_stats_hourly osh ON oa.consumer_key = osh.consumer_key
            INNER JOIN okapi_stats_monthly osm ON oa.consumer_key = osm.consumer_key
            INNER JOIN okapi_stats_temp ost ON oa.consumer_key = ost.consumer_key
            INNER JOIN okapi_submitted_objects oso ON oa.consumer_key = oso.consumer_key
            INNER JOIN okapi_tokens ot ON oa.consumer_key = ot.consumer_key
            WHERE oa.user_id = :userId
SQL
, [
            'userId' => $userId,
        ]);
    }

    private function deleteQueries(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM queries WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteWatches(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM watches_logqueue WHERE user_id = :userId', [
            'userId' => $userId,
        ]);

        $this->connection->executeQuery('DELETE FROM watches_notified WHERE user_id = :userId', [
            'userId' => $userId,
        ]);

        $this->connection->executeQuery('DELETE FROM watches_waiting WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }

    private function deleteWsSessions(int $userId): void
    {
        $this->connection->executeQuery('DELETE FROM ws_sessions WHERE user_id = :userId', [
            'userId' => $userId,
        ]);
    }
}
