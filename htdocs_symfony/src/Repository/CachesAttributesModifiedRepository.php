<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesAttributesModifiedRepository
{
    private const TABLE = 'caches_attributes_modified';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
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
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function fetchOneBy(array $where = []): GeoCachesAttributesModifiedEntity
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
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
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
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(GeoCachesAttributesModifiedEntity $entity): GeoCachesAttributesModifiedEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                self::TABLE,
                $databaseArray
        );

        $entity->cacheId = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(GeoCachesAttributesModifiedEntity $entity): GeoCachesAttributesModifiedEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                self::TABLE,
                $databaseArray,
                ['cache_id' => $entity->cacheId]
        );

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function remove(GeoCachesAttributesModifiedEntity $entity): GeoCachesAttributesModifiedEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['cache_id' => $entity->cacheId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    public function getDatabaseArrayFromEntity(GeoCachesAttributesModifiedEntity $entity): array
    {
        return [
                'cache_id' => $entity->cacheId,
                'attrib_id' => $entity->attribId,
                'date_modified' => $entity->dateModified,
                'was_set' => $entity->wasSet,
                'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @throws Exception
     */
    public function getEntityFromDatabaseArray(array $data): GeoCachesAttributesModifiedEntity
    {
        $entity = new GeoCachesAttributesModifiedEntity();
        $entity->cacheId = (int)$data['cache_id'];
        $entity->attribId = (int)$data['attrib_id'];
        $entity->dateModified = new DateTime($data['date_modified']);
        $entity->wasSet = (int)$data['was_set'];
        $entity->restoredBy = (int)$data['restored_by'];

        return $entity;
    }
}
