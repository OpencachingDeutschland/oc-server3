<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\GeoCacheTypeEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class CacheTypeRepository
{
    const TABLE = 'cache_type';

    /** @var Connection */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws Exception
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAll()
    : array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

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
     * @param array $where
     *
     * @return GeoCacheTypeEntity
     * @throws RecordNotFoundException
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchOneBy(array $where = [])
    : GeoCacheTypeEntity {
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

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     *
     * @return array
     * @throws Exception
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchBy(array $where = [])
    : array {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

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
     * @param GeoCacheTypeEntity $entity
     *
     * @return GeoCacheTypeEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(GeoCacheTypeEntity $entity)
    : GeoCacheTypeEntity {
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
     * @param GeoCacheTypeEntity $entity
     *
     * @return GeoCacheTypeEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(GeoCacheTypeEntity $entity)
    : GeoCacheTypeEntity {
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
     * @param GeoCacheTypeEntity $entity
     *
     * @return GeoCacheTypeEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidArgumentException
     */
    public function remove(GeoCacheTypeEntity $entity)
    : GeoCacheTypeEntity {
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
     * @param GeoCacheTypeEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCacheTypeEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'trans_id' => $entity->transId,
            'ordinal' => $entity->ordinal,
            'short' => $entity->short,
            'de' => $entity->de,
            'en' => $entity->en,
            'icon_large' => $entity->iconLarge,
            'short2' => $entity->short2,
            'short2_trans_id' => $entity->short2TransId,
            'kml_name' => $entity->kmlName,
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCacheTypeEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCacheTypeEntity {
        $entity = new GeoCacheTypeEntity();
        $entity->id = (int) $data['id'];
        $entity->name = (string) $data['name'];
        $entity->transId = (int) $data['trans_id'];
        $entity->ordinal = (int) $data['ordinal'];
        $entity->short = (string) $data['short'];
        $entity->de = (string) $data['de'];
        $entity->en = (string) $data['en'];
        $entity->iconLarge = (string) $data['icon_large'];
        $entity->short2 = (string) $data['short2'];
        $entity->short2TransId = (int) $data['short2_trans_id'];
        $entity->kmlName = (string) $data['kml_name'];

        return $entity;
    }
}
