<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\LogTypesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class LogTypesRepository
{
    private const TABLE = 'log_types';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
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
     * @throws Exception
     */
    public function fetchOneBy(array $where = []): LogTypesEntity
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
     * Fetches a logType by its id.
     *
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneById(int $id): LogTypesEntity
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->where('id = :id')
                ->setParameter('id', $id)
                ->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(
                    sprintf(
                            'Record with id #%s not found',
                            $id
                    )
            );
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(LogTypesEntity $entity): LogTypesEntity
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
    public function update(LogTypesEntity $entity): LogTypesEntity
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
    public function remove(LogTypesEntity $entity): LogTypesEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['id' => $entity->id]
        );

        $entity->id = 0;

        return $entity;
    }

    public function getDatabaseArrayFromEntity(LogTypesEntity $entity): array
    {
        return [
                'id' => $entity->id,
                'name' => $entity->name,
                'trans_id' => $entity->transId,
                'permission' => $entity->permission,
                'cache_status' => $entity->cacheStatus,
                'de' => $entity->de,
                'en' => $entity->en,
                'icon_small' => $entity->iconSmall,
                'allow_rating' => $entity->allowRating,
                'require_password' => $entity->requirePassword,
                'maintenance_logs' => $entity->maintenanceLogs,
        ];
    }

    public function getEntityFromDatabaseArray(array $data): LogTypesEntity
    {
        $entity = new LogTypesEntity();
        $entity->id = (int)$data['id'];
        $entity->name = (string)$data['name'];
        $entity->transId = (int)$data['trans_id'];
        $entity->permission = (string)$data['permission'];
        $entity->cacheStatus = (int)$data['cache_status'];
        $entity->de = (string)$data['de'];
        $entity->en = (string)$data['en'];
        $entity->iconSmall = (string)$data['icon_small'];
        $entity->allowRating = (int)$data['allow_rating'];
        $entity->requirePassword = (int)$data['require_password'];
        $entity->maintenanceLogs = (int)$data['maintenance_logs'];

        return $entity;
    }
}
