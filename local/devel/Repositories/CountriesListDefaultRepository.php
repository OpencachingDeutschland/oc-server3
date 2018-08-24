<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CountriesListDefaultRepository
{
    const TABLE = 'countries_list_default';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return CountriesListDefaultEntity[]
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
     * @return CountriesListDefaultEntity
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
     * @return CountriesListDefaultEntity[]
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
     * @param CountriesListDefaultEntity $entity
     * @return CountriesListDefaultEntity
     */
    public function create(CountriesListDefaultEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->lang = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param CountriesListDefaultEntity $entity
     * @return CountriesListDefaultEntity
     */
    public function update(CountriesListDefaultEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['lang' => $entity->lang]
        );

        return $entity;
    }

    /**
     * @param CountriesListDefaultEntity $entity
     * @return CountriesListDefaultEntity
     */
    public function remove(CountriesListDefaultEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['lang' => $entity->lang]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param CountriesListDefaultEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(CountriesListDefaultEntity $entity)
    {
        return [
            'lang' => $entity->lang,
            'show' => $entity->show,
        ];
    }

    /**
     * @param array $data
     * @return CountriesListDefaultEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new CountriesListDefaultEntity();
        $entity->lang = (string) $data['lang'];
        $entity->show = (string) $data['show'];

        return $entity;
    }
}
