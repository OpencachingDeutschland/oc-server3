<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\CachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesRepository
{
    const TABLE = 'caches';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return CachesEntity[]
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
     * @return CachesEntity
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
            //            throw new RecordNotFoundException('Record with given where clause not found');
        } else {
            return $this->getEntityFromDatabaseArray($result);
        }
    }

    /**
     * @return CachesEntity[]
     */
    public function fetchBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->orWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            //            throw new RecordsNotFoundException('No records with given where clause found');
        } else {
            $entities = [];

            foreach ($result as $item) {
                $entities[] = $this->getEntityFromDatabaseArray($item);
            }
        }

        return $entities;
    }

    /**
     * @return CachesEntity
     */
    public function getIdByWP(string $wp = '')
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if ($wp != '') {
            $queryBuilder->where('wp_oc = ' . $queryBuilder->createNamedParameter($wp));
            $queryBuilder->orWhere('wp_gc = ' . $queryBuilder->createNamedParameter($wp));
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            //            throw new RecordNotFoundException('Record with given where clause not found');
        } else {
            return $result['cache_id'];
        }
    }

    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new CachesEntity();

        $entity->setCacheId((int) $data['cache_id']);
        $entity->setOCid((string) $data['wp_oc']);
        $entity->setGCid((string) $data['wp_gc']);
        $entity->setName((string) $data['name']);
        $entity->setUserId((int) $data['user_id']);
        // ..

        return $entity;
    }
}