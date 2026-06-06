<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Exception;
use Oc\Entity\SupportBonuscachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SupportBonuscachesRepository
{
    private const TABLE = 'support_bonuscaches';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): SupportBonuscachesEntity
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
    public function create(SupportBonuscachesEntity $entity): SupportBonuscachesEntity
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(SupportBonuscachesEntity $entity): SupportBonuscachesEntity
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
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidArgumentException
     */
    public function remove(SupportBonuscachesEntity $entity): SupportBonuscachesEntity
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

    public function getDatabaseArrayFromEntity(SupportBonuscachesEntity $entity): array
    {
        return [
                'id' => $entity->id,
                'wp_oc' => $entity->wpOc,
                'is_bonus_cache' => $entity->isBonusCache,
                'belongs_to_bonus_cache' => $entity->belongsToBonusCache,
        ];
    }

    public function getEntityFromDatabaseArray(array $data): SupportBonuscachesEntity
    {
        $entity = new SupportBonuscachesEntity();
        $entity->id = (int)$data['id'];
        $entity->wpOc = (string)$data['wp_oc'];
        $entity->isBonusCache = (bool)$data['is_bonus_cache'];
        $entity->belongsToBonusCache = (string)$data['belongs_to_bonus_cache'];

        return $entity;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update_or_create_bonus_entry(string $wpID, string $toBonusCache, bool $setAsBonusCache = false): void
    {
        try {
            $entity = $this->fetchOneBy(['wp_oc' => $wpID]);
        } catch (Exception $exception) {
            $entity = new SupportBonuscachesEntity($wpID, $setAsBonusCache, $toBonusCache);
            $this->create($entity);
        }

        if ($setAsBonusCache === true) {
            $entity->isBonusCache = true;
        }

        if (!empty($toBonusCache)) {
            $entity->belongsToBonusCache = $toBonusCache;
        }

        $this->update($entity);
    }
}
