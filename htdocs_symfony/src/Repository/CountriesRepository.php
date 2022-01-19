<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\CountriesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class CountriesRepository
{
    const TABLE = 'countries';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchAll()
    : array
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
     *
     * @return CountriesEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : CountriesEntity {
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
     *
     * @return array
     * @throws RecordsNotFoundException
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
     * @param CountriesEntity $entity
     *
     * @return CountriesEntity
     * @throws RecordAlreadyExistsException
     * @throws DBALException
     */
    public function create(CountriesEntity $entity)
    : CountriesEntity {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->short = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param CountriesEntity $entity
     *
     * @return CountriesEntity
     * @throws DBALException
     * @throws RecordNotPersistedException
     */
    public function update(CountriesEntity $entity)
    : CountriesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['short' => $entity->short]
        );

        return $entity;
    }

    /**
     * @param CountriesEntity $entity
     *
     * @return CountriesEntity
     * @throws DBALException
     * @throws RecordNotPersistedException
     * @throws InvalidArgumentException
     */
    public function remove(CountriesEntity $entity)
    : CountriesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['short' => $entity->short]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param CountriesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(CountriesEntity $entity)
    : array {
        return [
            'short' => $entity->short,
            'name' => $entity->name,
            'trans_id' => $entity->transId,
            'de' => $entity->de,
            'en' => $entity->en,
            'list_default_de' => $entity->listDefaultDe,
            'sort_de' => $entity->sortDe,
            'list_default_en' => $entity->listDefaultEn,
            'sort_en' => $entity->sortEn,
            'adm_display2' => $entity->admDisplay2,
            'adm_display3' => $entity->admDisplay3,
        ];
    }

    /**
     * @param array $data
     *
     * @return CountriesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : CountriesEntity {
        $entity = new CountriesEntity();
        $entity->short = (string) $data['short'];
        $entity->name = (string) $data['name'];
        $entity->transId = (int) $data['trans_id'];
        $entity->de = (string) $data['de'];
        $entity->en = (string) $data['en'];
        $entity->listDefaultDe = (int) $data['list_default_de'];
        $entity->sortDe = (string) $data['sort_de'];
        $entity->listDefaultEn = (int) $data['list_default_en'];
        $entity->sortEn = (string) $data['sort_en'];
        $entity->admDisplay2 = (int) $data['adm_display2'];
        $entity->admDisplay3 = (int) $data['adm_display3'];

        return $entity;
    }
}
