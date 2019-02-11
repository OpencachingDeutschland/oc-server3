<?php

namespace Oc\GeoCache\Persistence\GeoCacheLog;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheLogRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'cache_logs';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all GeoCacheLogs.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return GeoCacheLogEntity[]
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * Fetches a GeoCacheLog by given where clause.
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchOneBy(array $where = []): ?GeoCacheLogEntity
    {
        $queryBuilder = $this->connection->createQueryBuilder()
             ->select('*')
             ->from(self::TABLE)
             ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Fetches all GeoCacheLogs by given where clause.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return GeoCacheLogEntity[]
     */
    public function fetchBy(array $where = []): array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
             ->select('*')
             ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $geoCaches = [];

        foreach ($result as $item) {
            $geoCaches[] = $this->getEntityFromDatabaseArray($item);
        }

        return $geoCaches;
    }

    /**
     * Fetch latest user geo cache log.
     *
     * @throws RecordNotFoundException
     */
    public function getLatestUserLog(int $userId): GeoCacheLogEntity
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :userId')
            ->orderBy('date', 'DESC')
            ->setParameter('userId', $userId)
            ->setMaxResults(1);

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Creates a GeoCacheLog in the database.
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Update a GeoCacheLog in the database.
     *
     * @throws RecordNotPersistedException
     */
    public function update(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['id' => $entity->id]
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * Removes a GeoCacheLog from the database.
     *
     * @throws RecordNotPersistedException
     */
    public function remove(GeoCacheLogEntity $entity): GeoCacheLogEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * Maps the given entity to the database array.
     */
    public function getDatabaseArrayFromEntity(GeoCacheLogEntity $entity): array
    {
        return [
            'id' => $entity->id,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated->format(DateTime::ATOM),
            'entry_last_modified' => $entity->entryLastModified->format(DateTime::ATOM),
            'last_modified' => $entity->lastModified->format(DateTime::ATOM),
            'okapi_syncbase' => $entity->okapiSyncbase,
            'log_last_modified' => $entity->logLastModified->format(DateTime::ATOM),
            'cache_id' => $entity->cacheId,
            'user_id' => $entity->userId,
            'type' => $entity->type,
            'oc_team_comment' => $entity->ocTeamComment,
            'date' => $entity->date->format(DateTime::ATOM),
            'order_date' => $entity->orderDate->format(DateTime::ATOM),
            'needs_maintenance' => $entity->needsMaintenance,
            'listing_outdated' => $entity->listingOutdated,
            'text' => $entity->text,
            'text_html' => $entity->textHtml,
            'text_htmledit' => $entity->textHtmledit,
            'owner_notified' => $entity->ownerNotified,
            'picture' => $entity->picture,
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): GeoCacheLogEntity
    {
        $entity = new GeoCacheLogEntity();
        $entity->id = (int) $data['id'];
        $entity->uuid = $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->entryLastModified = new DateTime($data['entry_last_modified']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->okapiSyncbase = (int) $data['okapi_syncbase'];
        $entity->logLastModified = new DateTime($data['log_last_modified']);
        $entity->cacheId = (int) $data['cache_id'];
        $entity->userId = (int) $data['user_id'];
        $entity->type = (int) $data['type'];
        $entity->ocTeamComment = (bool) $data['oc_team_comment'];
        $entity->date = new DateTime($data['date']);
        $entity->orderDate = new DateTime($data['order_date']);
        $entity->needsMaintenance = (bool) $data['needs_maintenance'];
        $entity->listingOutdated = (bool) $data['listing_outdated'];
        $entity->text = $data['text'];
        $entity->textHtml = (bool) $data['text_html'];
        $entity->textHtmledit = (bool) $data['text_htmledit'];
        $entity->ownerNotified = (bool) $data['owner_notified'];
        $entity->picture = (int) $data['picture'];

        return $entity;
    }
}
