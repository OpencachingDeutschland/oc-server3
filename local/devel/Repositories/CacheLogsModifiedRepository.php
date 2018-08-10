<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLogsModifiedRepository
{
    const TABLE = 'cache_logs_modified';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheLogsModifiedEntity[]
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
     * @param array $where
     * @return GeoCacheLogsModifiedEntity
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
     * @param array $where
     * @return GeoCacheLogsModifiedEntity[]
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
     * @param GeoCacheLogsModifiedEntity $entity
     * @return GeoCacheLogsModifiedEntity
     */
    public function create(GeoCacheLogsModifiedEntity $entity)
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
     * @param GeoCacheLogsModifiedEntity $entity
     * @return GeoCacheLogsModifiedEntity
     */
    public function update(GeoCacheLogsModifiedEntity $entity)
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
     * @param GeoCacheLogsModifiedEntity $entity
     * @return GeoCacheLogsModifiedEntity
     */
    public function remove(GeoCacheLogsModifiedEntity $entity)
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
     * @param GeoCacheLogsModifiedEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheLogsModifiedEntity $entity)
    {
        return [
        'id' => $entity->id,
        'uuid' => $entity->uuid,
        'node' => $entity->node,
        'date_created' => $entity->dateCreated,
        'entry_last_modified' => $entity->entryLastModified,
        'last_modified' => $entity->lastModified,
        'log_last_modified' => $entity->logLastModified,
        'cache_id' => $entity->cacheId,
        'user_id' => $entity->userId,
        'type' => $entity->type,
        'oc_team_comment' => $entity->ocTeamComment,
        'date' => $entity->date,
        'needs_maintenance' => $entity->needsMaintenance,
        'listing_outdated' => $entity->listingOutdated,
        'text' => $entity->text,
        'text_html' => $entity->textHtml,
        'modify_date' => $entity->modifyDate,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheLogsModifiedEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheLogsModifiedEntity();
        $entity->id = $data['id'];
        $entity->uuid = $data['uuid'];
        $entity->node = $data['node'];
        $entity->dateCreated = $data['date_created'];
        $entity->entryLastModified = $data['entry_last_modified'];
        $entity->lastModified = $data['last_modified'];
        $entity->logLastModified = $data['log_last_modified'];
        $entity->cacheId = $data['cache_id'];
        $entity->userId = $data['user_id'];
        $entity->type = $data['type'];
        $entity->ocTeamComment = $data['oc_team_comment'];
        $entity->date = $data['date'];
        $entity->needsMaintenance = $data['needs_maintenance'];
        $entity->listingOutdated = $data['listing_outdated'];
        $entity->text = $data['text'];
        $entity->textHtml = $data['text_html'];
        $entity->modifyDate = $data['modify_date'];

        return $entity;
    }
}
