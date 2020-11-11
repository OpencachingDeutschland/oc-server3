<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLogsArchivedRepository
{
    const TABLE = 'cache_logs_archived';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheLogsArchivedEntity[]
     */
    public function fetchAll()
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
     * @return GeoCacheLogsArchivedEntity
     */
    public function fetchOneBy(array $where = [])
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
     * @return GeoCacheLogsArchivedEntity[]
     */
    public function fetchBy(array $where = [])
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

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @return GeoCacheLogsArchivedEntity
     */
    public function create(GeoCacheLogsArchivedEntity $entity)
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
     * @return GeoCacheLogsArchivedEntity
     */
    public function update(GeoCacheLogsArchivedEntity $entity)
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

        return $entity;
    }

    /**
     * @return GeoCacheLogsArchivedEntity
     */
    public function remove(GeoCacheLogsArchivedEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheLogsArchivedEntity $entity)
    {
        return [
            'id' => $entity->id,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated,
            'entry_last_modified' => $entity->entryLastModified,
            'last_modified' => $entity->lastModified,
            'okapi_syncbase' => $entity->okapiSyncbase,
            'log_last_modified' => $entity->logLastModified,
            'cache_id' => $entity->cacheId,
            'user_id' => $entity->userId,
            'type' => $entity->type,
            'oc_team_comment' => $entity->ocTeamComment,
            'date' => $entity->date,
            'order_date' => $entity->orderDate,
            'needs_maintenance' => $entity->needsMaintenance,
            'listing_outdated' => $entity->listingOutdated,
            'text' => $entity->text,
            'text_html' => $entity->textHtml,
            'text_htmledit' => $entity->textHtmledit,
            'owner_notified' => $entity->ownerNotified,
            'picture' => $entity->picture,
            'deletion_date' => $entity->deletionDate,
            'deleted_by' => $entity->deletedBy,
            'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @return GeoCacheLogsArchivedEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheLogsArchivedEntity();
        $entity->id = (int) $data['id'];
        $entity->uuid = (string) $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->entryLastModified = new DateTime($data['entry_last_modified']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->okapiSyncbase = (string) $data['okapi_syncbase'];
        $entity->logLastModified = new DateTime($data['log_last_modified']);
        $entity->cacheId = (int) $data['cache_id'];
        $entity->userId = (int) $data['user_id'];
        $entity->type = (int) $data['type'];
        $entity->ocTeamComment = (int) $data['oc_team_comment'];
        $entity->date = new DateTime($data['date']);
        $entity->orderDate = new DateTime($data['order_date']);
        $entity->needsMaintenance = (int) $data['needs_maintenance'];
        $entity->listingOutdated = (int) $data['listing_outdated'];
        $entity->text = (string) $data['text'];
        $entity->textHtml = (int) $data['text_html'];
        $entity->textHtmledit = (int) $data['text_htmledit'];
        $entity->ownerNotified = (int) $data['owner_notified'];
        $entity->picture = $data['picture'];
        $entity->deletionDate = new DateTime($data['deletion_date']);
        $entity->deletedBy = (int) $data['deleted_by'];
        $entity->restoredBy = (int) $data['restored_by'];

        return $entity;
    }
}
