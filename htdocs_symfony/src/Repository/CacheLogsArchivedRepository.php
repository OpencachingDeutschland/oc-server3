<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCacheLogsArchivedEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLogsArchivedRepository
{
    private const TABLE = 'cache_logs_archived';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
     * @throws \Exception
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

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
     * @throws RecordNotFoundException
     * @throws Exception
     * @throws \Exception
     */
    public function fetchOneBy(array $where = []): GeoCacheLogsArchivedEntity
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            //            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(GeoCacheLogsArchivedEntity $entity): GeoCacheLogsArchivedEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                self::TABLE,
                $databaseArray
        );

        $entity->id = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(GeoCacheLogsArchivedEntity $entity): GeoCacheLogsArchivedEntity
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
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(GeoCacheLogsArchivedEntity $entity): GeoCacheLogsArchivedEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['id' => $entity->id]
        );

        $entity->cacheId = 0;

        return $entity;
    }

    public function getDatabaseArrayFromEntity(GeoCacheLogsArchivedEntity $entity): array
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
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data): GeoCacheLogsArchivedEntity
    {
        $entity = new GeoCacheLogsArchivedEntity();
        $entity->id = (int)$data['id'];
        $entity->uuid = (string)$data['uuid'];
        $entity->node = (int)$data['node'];
        $entity->dateCreated = $data['date_created'];
        $entity->entryLastModified = $data['entry_last_modified'];
        $entity->lastModified = date('Y-m-d H:i:s');
        $entity->okapiSyncbase = (string)$data['okapi_syncbase'];
        $entity->logLastModified = $data['log_last_modified'];
        $entity->cacheId = (int)$data['cache_id'];
        $entity->userId = (int)$data['user_id'];
        $entity->type = (int)$data['type'];
        $entity->ocTeamComment = (int)$data['oc_team_comment'];
        $entity->date = $data['date'];
        $entity->orderDate = $data['order_date'];
        $entity->needsMaintenance = (int)$data['needs_maintenance'];
        $entity->listingOutdated = (int)$data['listing_outdated'];
        $entity->text = (string)$data['text'];
        $entity->textHtml = (int)$data['text_html'];
        $entity->textHtmledit = (int)$data['text_htmledit'];
        $entity->ownerNotified = (int)$data['owner_notified'];
        $entity->picture = (int)$data['picture'];
        $entity->deletionDate = $data['deletion_date'];
        $entity->deletedBy = (int)$data['deleted_by'];
        $entity->restoredBy = (int)$data['restored_by'];

        return $entity;
    }
}
